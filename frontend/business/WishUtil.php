<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/15
 * Time: 13:19
 */

namespace frontend\business;

use common\components\CaculateUtil;
use common\components\OssUtil;
use common\components\PhpLock;
use common\components\StatusUtil;
use common\components\UsualFunForStringHelper;
use common\models\AccountInfo;
use common\models\HotOrderExtend;
use common\models\Wish;
use common\models\WishComment;
use common\components\SystemParamsUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\AddWishCommentByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CheckRecordSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\WishSaveByTrans;
use yii\base\Exception;
use yii\log\Logger;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BalanceSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\WishSaveForToBalance;


/**
 * Class 愿望业务类
 * @package frontend\business
 */
class WishUtil
{

    /**
     * 禁止评论
     * @param $comment
     * @param $error
     * @return bool
     */
    public static function ForbidComment($comment,&$error)
    {
        if(!($comment instanceof WishComment))
        {
            $error = '不是愿望评论记录';
            return false;
        }
        if(!$comment->save())
        {
            $error = '禁止评论保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($comment->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }
    /**
     * 根据id获取评论
     * @param $comment_id
     * @return null|static
     */
    public static function GetWishCommentById($comment_id)
    {
        return WishComment::findOne(['wish_comment_id'=>$comment_id]);
    }

    /**
     * 检测愿望会否可以取消
     * @param $wish
     * @param $user
     * @param $error
     * @param $is_back_cancel 是否后台取消
     * @return bool
     */
    public static function CheckWishCancel($wish,$user,&$error,$is_back_cancel = false)
    {
        if($wish->is_finish === 2)
        {
            $error = '愿望已经实现不能取消';
            return false;
        }
        if($wish->finish_status > 1)
        {
            $error = '愿望已过期或已完成，不能取消';
            return false;
        }
        if(!$is_back_cancel)
        {
            if($wish->publish_user_id !== $user->account_id)
            {
                $error = '不是您发布的愿望，不能取消';
                return false;
            }
        }
        $curDate = date('Y-m-d');
        if($curDate > $wish->end_date)
        {
            $error = '愿望已经过期，无需取消';
            return false;
        }
        if($wish->status === 0)
        {
            $error = '已经取消无需重复取消';
            return false;
        }
        return true;
    }

    /**
     * 取消愿望
     * @param $wish
     * @param $user
     * @param $error
     */
    public static function CancelWish($wish,$user,&$error)
    {
        if(!($wish instanceof Wish))
        {
            $error = '不是愿望对象';
            return false;
        }
        if(!($user instanceof AccountInfo))
        {
            $error = '不是用户对象';
            return false;
        }
        if(!self::CheckWishCancel($wish,$user,$error))
        {
            return false;
        }
        //设置取消状态、设置退款状态、业务日志、消息、审核记录
        $transActions = [];

        $transActions[] = new WishSaveByTrans($wish,['modify_type'=>'cancel_wish','status'=>0]);

        $businessLog = BusinessLogUtil::GetBusinessLogNew('266', $user);
        $businessLog->remark5 = strval($wish->wish_id);
        $businessLog->remark6 = $wish->wish_name;
        //$businessLog->remark7 = strval($billInfo->account_info_id);
        $businessLog->remark9 = sprintf('%s提交了愿望【%s】的取消，等待审核',$user->nick_name,$wish->wish_name);

        $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'取消愿望业务日志存储异常']);

        $msgContent = sprintf('你取消了愿望【%s】，审核中',$wish->wish_name);
        $msg = MessageUtil::GetMsgNewModel('72',$msgContent,$user->account_id);
        $transActions[] = new MessageSaveForReward($msg);

        $checkRecord = BusinessCheckUtil::GetBusinessCheckModelNew(4,$wish->wish_id,$user);
        $transActions[] = new CheckRecordSaveForReward($checkRecord);

        if(!RewardUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 获取退款愿望记录
     * @param int $limit
     */
    public static function GetBackMoneyWish($limit=10)
    {
        if(empty($limit))
        {
            $limit = 10;
        }
        //过期或者取消 的愿望 并且处于退款中的记录
        $condition = ['and','is_finish=1','to_balance=1','back_status=2',['or','finish_status=4','status=0']];
        return Wish::find()->limit($limit)->where($condition)->all();
    }

    /**
     * 愿望金额转余额
     * @param $user_id
     * @param $fee_rate
     * @param $wish_id
     * @param $error
     * @return bool
     */
    public static function ChangeWishMoneyToBalance($user_id,$wish_id,$fee_rate, &$error)
    {
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error ='用户信息不存在';
            return false;
        }
        //将愿望设置成结束如果未结束，将所有金额转到余额、业务日志、消息信息、
        //条件 已经完成或已经结束  未转账
        $wish = WishUtil::GetWishRecordById($wish_id);
        if(!isset($wish))
        {
            $error ='愿望不存在';
            return false;
        }
        if($wish->publish_user_id !== intval($user_id))
        {
            $error = '不是愿望发起者不能提取愿望金额';
            return false;
        }
        $billInfo = PersonalUserUtil::GetUserBillInfoByUserId($user_id);
        if(!isset($billInfo))
        {
            $error = '账户余额信息不存在';
            return false;
        }
        $transActions = [];
        $transActions[] = new WishSaveForToBalance($wish);
        if(doubleval($fee_rate)<= 0)
        {
            $error = '手续费率必须大于0';
            return false;
        }
        $add_money = $wish->ready_reward_money + $wish->red_packets_money;
        $fee = $add_money * $fee_rate / 100.0;
        $fee = round($fee,2);//精确到两位，否则金额认证无法通过，数据库精确到两位
        $except_fee = $add_money - $fee;
        $transActions[] = new BalanceSaveForReward($billInfo,['modify_type'=>'add_balance','add_money'=>$except_fee]);

        $busiNessLog = BusinessLogUtil::GetBusinessLogNew(264,$user);
        $busiNessLog->remark5 = strval($wish->wish_id);
        $busiNessLog->remark6 = $wish->wish_name;
        $busiNessLog->remark7 = strval($billInfo->account_info_id);
        $busiNessLog->remark9 = sprintf('%s将愿望【%s】的金额【%s】转入了账户余额，手续费【%s】，手续费率【%s】，除手续费外金额【%s】，转前余额【%s】',
            $user->nick_name,
            $wish->wish_name,
            $add_money,
            $fee,
            $fee_rate,
            $except_fee,
            $billInfo->balance);

        $transActions[] = new BusinessLogSaveForReward($busiNessLog,['error'=>'愿望金额转入账户余额业务日志存储异常',
            'propertys'=>[
                'remark10'=>[
                    'model'=>'user_bill',
                    'attr'=>'attributes',
                    'key_method'=>'SetRemark10ByUserAccountInfo',
                ],
            ]]);
        $cont = sprintf('您成功将愿望【%s】的金额转入了账户余额，需要扣除%s%%手续费，扣除手续费后到账金额【%s】',
            $wish->wish_name,
            $fee_rate,
            $except_fee);
        $msg = MessageUtil::GetMsgNewModel(70,$cont,$user_id);
        $transActions[] = new MessageSaveForReward($msg);

        if(!RewardUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }


    /**
     * 获取过期愿望id
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function GetOverTimeWish($limit = 10)
    {
        if(empty($limit))
        {
            $limit = 10;
        }
//        $sql = 'select wish_id from my_wish where is_finish=1 and finish_status=1 and curdate()>end_date limit '.strval($limit);
//        $rcList = \Yii::$app->db->createCommand($sql)->queryAll();
//        return $rcList;
        return Wish::find()->limit($limit)->where(['and','is_finish=1','finish_status=1','curdate()>end_date'])->all();
    }


    /**
     * 获取已完成的支付
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function GetFinishWish($limit = 10)
    {
        if(empty($limit))
        {
            $limit = 10;
        }
//        $sql = 'select wish_id from my_wish where is_finish=2 and finish_status=1 and curdate()>end_date limit '.strval($limit);
//        $rcList = \Yii::$app->db->createCommand($sql)->queryAll();
//        return $rcList;
        return Wish::find()->limit($limit)->where(['and','is_finish=2','finish_status=1','curdate()>end_date'])->all();
    }


    /**
     * 获取个人最新的愿望
     * @param $user_id
     * @param $limit
     */
    public static function GetWishListByUserId($user_id,$limit)
    {
        if(empty($limit))
        {
            $limit = 10;
        }
        $wishList = Wish::find()->limit($limit)->where(['and',['publish_user_id'=>$user_id,'status'=>'1']])->all();
        return $wishList;
    }
    /**
     * 根据条件获取愿望列表
     * @param $flag  获取记录的标记up 、down、new
     * @param $start_id  开始记录id
     * @param $finish_status  完成状态
     * @param $user_id   用户id
     * @param $user_name  用户名
     * @param $title   愿望标题
     * @return static[]
     */
    public static function GetWishByCondition($flag,$start_id, $finish_status,$user_id,$user_name,$title,$wish_type_id=null)
    {
        $condition = 'status=1';
        if(!empty($finish_status))
        {
            $statusList = StatusUtil::GetStatusList($finish_status);
            $statusStr = implode(',',$statusList);
            $condition .= ' and finish_status in ('.$statusStr.')';;

        }
        else
        {
            $paramAry = [];
        }
        $offset = 0;
        switch($flag)
        {
            case 'up':
/*            $condition .= ' and wish_id > :wid';
            $paramAry[':wid'] = $start_id;*/
            $page_no = \Yii::$app->session['newpage'];
            if(!isset($page_no))
            {
                $page_no = 0;
            }
            if($page_no > 0)
            {
                $page_no = $page_no -1;
            }
            $offset = $page_no*5;
            \Yii::$app->session['newpage']= $page_no;
            //\Yii::getLogger()->log('up offest:'.$offset,Logger::LEVEL_ERROR);
                break;
            case 'down':
/*                $condition .= ' and wish_id < :wid';
                $paramAry[':wid'] = $start_id;*/
                $page_no = \Yii::$app->session['newpage'];
                if(!isset($page_no))
                {
                    $page_no = 0;
                }
                $page_no = $page_no +1;

                $offset = $page_no*5;
                \Yii::$app->session['newpage']= $page_no;
                //\Yii::getLogger()->log('down offest:'.$offset,Logger::LEVEL_ERROR);
                break;
            default:
                \Yii::$app->session['newpage']=0;
                break;
        }
        if(!empty($user_id))
        {
            $condition .= ' and publish_user_id=:pui';
            $paramAry[':pui'] = $user_id;
            //$condition['publish_user_id'] = $user_id;
        }
        $tempStr = '';
        if(!empty($user_name) || !empty($title))
        {
            if(!empty($user_name))
            {
                $tempStr .= ' publish_user_name like :pun';
                $paramAry[':pun'] = '%'.$user_name.'%';
            }
            if(!empty($title))
            {
                if(!empty($tempStr))
                {
                    $tempStr .= ' or wish_name like :tl';
                }
                else
                {
                    $tempStr .= ' wish_name like :tl';
                }
                $paramAry[':tl'] = '%'.$title.'%';
            }
            $tempStr = '('.$tempStr.')';
        }
        if(!empty($tempStr))
        {
            $condition .= ' and '.$tempStr;
        }

        if(!empty($wish_type_id))
        {
            $condition .= ' and wish_type_id=:wtd';
            $paramAry[':wtd'] = $wish_type_id;
        }
        //\Yii::getLogger()->log('wish_type_id:'.$wish_type_id.'flag:'.$flag.'start_id:'.$start_id,Logger::LEVEL_ERROR);
        //\Yii::getLogger()->log($condition,Logger::LEVEL_ERROR);
        $wishList = Wish::find()
            ->limit(5)
            ->offset($offset)
            ->where($condition, $paramAry)
            ->from('my_wish wh')
            ->innerJoin('my_wish_new_statistic wns','wns.wish_id=wh.wish_id')
            ->orderBy('wns.order_no desc, wns.modify_time desc')
            ->all();
        $wishIdaAry = [];
        foreach($wishList as $wish)
        {
            $wishIdaAry[] = $wish->wish_id;
        }
        //\Yii::getLogger()->log(var_export($wishIdaAry,true), Logger::LEVEL_ERROR);
        return $wishList;
    }

    /**
     * 获取推荐愿望
     * @param $flag
     * @param $start_id
     * @param $wish_type_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function GetRecommendWish($flag, $start_id,$wish_type_id)
    {
        $condition = 'status=1 and finish_status=1';
        $offset = 0;
        switch($flag)
        {
            case 'up':
/*                $condition .= ' and wish_id > :wid';
                $paramAry[':wid'] = $start_id;*/
                $page_no = \Yii::$app->session['recommendpage'];
                if(!isset($page_no))
                {
                    $page_no = 0;
                }
                if($page_no > 0)
                {
                    $page_no = $page_no -1;
                }
                $offset = $page_no*5;
                \Yii::$app->session['recommendpage']= $page_no;
                break;
            case 'down':
/*                $condition .= ' and wish_id < :wid';
                $paramAry[':wid'] = $start_id;*/
                $page_no = \Yii::$app->session['recommendpage'];
                if(!isset($page_no))
                {
                    $page_no = 0;
                }
                $page_no = $page_no +1;

                $offset = $page_no*5;
                \Yii::$app->session['recommendpage']= $page_no;
                break;
            default:
                \Yii::$app->session['recommendpage']=0;
                break;
        }
        if(!empty($wish_type_id))
        {
            $condition .= ' and wish_type_id=:wtd';
            $paramAry[':wtd'] = $wish_type_id;
        }
        $wishList = Wish::find()
            ->limit(5)
            ->offset($offset)
            ->from('my_wish wh')
            ->innerJoin('my_wish_recommend wr','wh.wish_id=wr.wish_id')
            ->where($condition, $paramAry)
            ->orderBy('wr.order_no desc')
            ->all();
        return $wishList;
    }

    /**
     * 获取附近愿望
     * ram $flag
     * @param $start_id
     * @param $wish_type_id
     * @param $lng  经度
     * @param $lat 纬度
     */
    public static  function GetNearByWish($flag, $start_id, $wish_type_id, $lng, $lat)
    {
        //获取附近的距离
        $nearByDis = SystemParamsUtil::GetSystemParam('system_nearby_distance');
        $condition= 'status=1 and finish_status between 1 and 2 and latitude > :lat2 -1 and latitude < :lat3 + 1 and longitude > :lng1 -1 and longitude < :lng2 +1 and ';
        $condition .= 'abs(round(6378.138*2*asin(sqrt(pow(sin((latitude*pi()/180-:lat*pi()/180)/2),2)+cos(latitude*pi()/180)*cos(:lat1*pi()/180)*pow(sin((longitude*pi()/180-:lng*pi()/180)/2),2)))*1000))<=:dis';
        $paramAry = [':lat2'=>$lat,':lat3'=>$lat,':lng1'=>$lng,':lng2'=>$lng,':lat'=> $lat,':lat1'=>$lat,':lng'=>$lng,':dis'=>$nearByDis];
        switch($flag)
        {
            case 'up':
                $condition .= ' and wish_id > :wid';
                $paramAry[':wid'] = $start_id;
                break;
            case 'down':
                $condition .= ' and wish_id < :wid';
                $paramAry[':wid'] = $start_id;
                break;
            default:
                break;
        }
        if(!empty($wish_type_id))
        {
            $condition .= ' and wish_type_id=:wtd';
            $paramAry[':wtd'] = $wish_type_id;
        }
        $wishList = Wish::find()->limit(5)->orderBy('wish_id desc')->where($condition, $paramAry)->all();
        return $wishList;
    }


    /**
     * 获取愿望排行版（热度）
     * @param $flag
     * @param $start_id
     * @param $wish_type_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function GetTopBoardWish($flag, $start_id,$wish_type_id)
    {
        $condition='status=1 and finish_status between 1 and 2';
        $paramAry=[];
        $offset = 0;
        switch($flag)
        {
            case 'up':
/*                $condition .= ' and hot_num > :wid';
                $paramAry[':wid'] = $start_id;*/
                $page_no = \Yii::$app->session['topboardpage'];
                if(!isset($page_no))
                {
                    $page_no = 0;
                }
                if($page_no > 0)
                {
                    $page_no = $page_no -1;
                }
                $offset = $page_no*5;
                \Yii::$app->session['topboardpage']= $page_no;
                break;
            case 'down':
/*                $condition .= ' and hot_num < :wid';
                $paramAry[':wid'] = $start_id;*/
                $page_no = \Yii::$app->session['topboardpage'];
                if(!isset($page_no))
                {
                    $page_no = 0;
                }
                $page_no = $page_no +1;

                $offset = $page_no*5;
                \Yii::$app->session['topboardpage']= $page_no;
                break;
            default:
                \Yii::$app->session['topboardpage']=0;
                break;
        }
        if(!empty($wish_type_id))
        {
            $condition .= ' and wish_type_id=:wid';
            $paramAry[':wid'] = $wish_type_id;
        }
        $wishList = Wish::find()
            ->from('my_wish wh')
            ->offset($offset)
            ->limit(5)
            ->innerJoin('my_hot_order_extend hoe','wh.wish_id=hoe.wish_id')
            ->orderBy('hoe.order_no desc,hot_num desc')
            ->where($condition, $paramAry)->all();
        return $wishList;
    }

    /**
     * 格式化愿望记录
     * @param $wishRecordList
     */
    public static function GetFormateForWishList($wishRecordList,$regionInfo = null,$emptyField = [])
    {
        $rst = [];
        if(!isset($wishRecordList) || empty($wishRecordList))
        {
            return $rst;
        }
        $user_list=[];
        $fields = ['wish_id','to_balance','hot_num','publish_user_id','is_finish','finish_status','publish_user_name','wish_name','discribtion','wish_type_id','wish_money','ready_reward_money','red_packets_money','pic1','pic2','pic3','pic4','pic5','pic6','end_date','back_type','back_dis','reward_num','collect_num','view_num','comment_num'];
        $dataRecord = [];
        $len = count($fields);
        foreach($wishRecordList as $oneWish)
        {
            for($i = 0; $i < $len; $i ++)
            {
                $dataRecord[$fields[$i]] = $oneWish[$fields[$i]];
            }
            if(is_array($emptyField) && !empty($emptyField))
            {
                foreach($emptyField as $field)
                {
                    $dataRecord[$field] = '';//清空为空字段的数据
                }
            }

            //性别、距离、图片、天数
            if(isset($user_list[$oneWish->publish_user_id]))
            {
                $user= $user_list[$oneWish->publish_user_id];
            }
            else
            {
                $user = PersonalUserUtil::GetAccontInfoById($oneWish->publish_user_id);
                $user_list[$oneWish->publish_user_id] =$user;
            }
            $dataRecord['publish_user_name'] = $user->nick_name;
            $dataRecord['sex']=empty($user->sex)?'':$user->sex;
            $dataRecord['distance']= $regionInfo == null?'--':(CaculateUtil::GetDistance($regionInfo['longitude'],$regionInfo['latitude'],$oneWish->longitude,$oneWish->latitude));
            $dataRecord['user_pic'] = empty($user->pic)?'':$user->pic;
            $leftDays = intval((strtotime($dataRecord['end_date']) - strtotime(date('Y-m-d'))) / (3600 * 24));

            if($oneWish->is_finish === 2)
            {
                if($oneWish->finish_status !== 1 || $leftDays < 0)
                {
                    $leftDays = '已结束';
                }
            }
            else if($oneWish->finish_status !== 1 || $leftDays < 0)
            {
                $leftDays = '已经过期';
            }
            if($leftDays === 0)
            {
                $leftDays = 1;
            }
            $dataRecord['wish_over_left_days'] = strval($leftDays);
            $reward_max_list = self::GetRewardMaxList($oneWish->wish_id);
            $dataRecord['reward_max_list'] = $reward_max_list;

            $rst[] = $dataRecord;
        }
        //\Yii::getLogger()->log(var_export($rst,true),Logger::LEVEL_ERROR);
        return $rst;
    }

    /**
     * 获取某个愿望打赏金额前5的列表
     * @param $wish_id
     * @return []
     */
    public static function GetRewardMaxList($wish_id)
    {
        $sql = 'SELECT DISTINCT ai.pic as user_pic,account_id as user_id FROM `my_reward_list` rl inner join my_account_info ai on rl.`reward_user_id` = ai.account_id where wish_id=:wid and rl.pay_status = 2 order by `reward_money` desc limit 5';
        $rcList = \Yii::$app->db->createCommand($sql,[':wid'=>$wish_id])->queryAll();
        $out = [];
        if(empty($rcList))
        {
            return $out;
        }
        foreach($rcList as $one)
        {
            $out[] = $one;
        }
       return $out;
    }

    /**
     * 保存评论
     * @param $attrs
     * @param $error
     * @return bool
     */
    public static function AddWishComment($attrs,$wish, &$error)
    {
        $error = '';
        $model = new WishComment();
        $model->attributes = $attrs;
        $transActions = [];
        $transActions[] = new AddWishCommentByTrans($model);

        $transActions[] = new WishSaveByTrans($wish,['modify_type'=>'comment']);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 获取愿望评论
     * @param $flag
     * @param $start_id
     * @param $wish_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function GetWishCommentListByCondition($flag, $start_id, $wish_id)
    {
        $conditon = 'where 1=1';// 'wish_id=:wid';
        $paramAry = [
            ':wid'=>$wish_id,
            ':wid1'=>$wish_id
        ];
        switch($flag)
        {
            case 'up':
                $conditon .= ' and h.rowid > :wci';
                $paramAry[':wci'] = $start_id;
                break;
            case 'down':
                $conditon .= ' and h.rowid < :wci';
                $paramAry[':wci'] = $start_id;
                break;
            default;
        }
        /* 执行有错，之前是可以的
select @row_id:=@row_id + 1,pic,user_id,content_title,create_time,content,data_type,reward_money_except_packets,red_packets_money,is_base_verify from
(
SELECT remark1 as pic,talk_user_id as user_id,`content_title`,`create_time`,`content`,1 as data_type,null as `reward_money_except_packets`,null as red_packets_money, null as is_base_verify FROM `my_wish_comment`where wish_id=:wid
union all
SELECT remark1 as pic,reward_user_id as user_id,concat(`reward_user_name`,' ',cast(`reward_money` as char(10))) as content_title,`create_time` ,remark2 as content,2 as data_type,`reward_money_except_packets` ,`red_packets_money` ,remark3 as is_base_verify FROM `my_reward_list`where wish_id=:wid1
    ) v ,(select @row_id:=0) d  order by create_time desc
         */
        $sql ='
select * from (
select (@row_id:=@row_id + 1) as rowid,pic,user_id,user_name,content_title,create_time,content,data_type,reward_money_except_packets,red_packets_money,is_base_verify from
(
SELECT ai.pic,talk_user_id as user_id,ai.nick_name as user_name,cast(`content_title` as char(1000))as content_title,wc.`create_time`,cast(`content` as char(1000)) as content,1 as data_type,0.00 as `reward_money_except_packets`,0.00 as red_packets_money, 0 as is_base_verify FROM `my_wish_comment` wc inner join my_account_info ai on wc.talk_user_id = ai.account_id where wc.status = 1 and wish_id=:wid
union all
SELECT ai.pic,reward_user_id as user_id,ai.nick_name as user_name,cast(`reward_money` as char(10)) as content_title,rl.`create_time` ,cast(rl.remark2 as char(1000)) as content,2 as data_type,reward_money as `reward_money_except_packets` ,ifnull(first_red_packet_money,0.0) as`red_packets_money` ,(case when first_red_packet_id is null then 1 else cast(ifnull(rl.remark3,\'0\') as SIGNED) end) as is_base_verify FROM `my_reward_list` rl inner join my_account_info ai on rl.reward_user_id = ai.account_id where wish_id=:wid1 and pay_status = 2
) v ,(select @row_id:=0) d order by create_time ASC
) h '.$conditon.' order by rowid desc  limit 10
';
        $recordList = \Yii::$app->db->createCommand($sql, $paramAry)->queryAll();
        return $recordList;
    }


    /**
     * 获取格式化评论数据
     * @param $recordList
     */
    public static function GetFormateWishCommentList($recordList)
    {
            $fields = ['rowid','pic','user_id','user_name','content','content_title','create_time','data_type','reward_money_except_packets','red_packets_money','is_base_verify'];
        $rst = [];
        if(!isset($recordList) || empty($recordList))
        {
            return $rst;
        }
        $len = count($fields);
        foreach($recordList as $record)
        {
            $tmpAry = [];
            for($i =0; $i < $len; $i ++)
            {
                $tmpAry[$fields[$i]] = $record[$fields[$i]];
            }
            $rst[] = $tmpAry;
        }
        return $rst;
    }

    /**
     * 根据id获取愿望
     * @param $wish_id
     * @return null|static
     */
    public static function GetWishRecordById($wish_id)
    {
        $wish = Wish::findOne([
            'wish_id'=>$wish_id
        ]);
        return $wish;
    }

    /**
     * 检查愿望是否可以支持
     * @param $wish
     * @param $pay_type
     * @param $user
     */
    public static function CheckWishCouldReward($wish,$pay_type,$user,&$error)
    {
        $error = '';
        //pay_type 1余额支付 2美愿基金支付 3支付宝支付 4微信支付 5连连支付
        if($pay_type != '2' && $user->account_id === $wish->publish_user_id)
        {
            $error = '该支付方式下不能打赏自己';
            return false;
        }
        if($pay_type == '2' && $user->account_id !== $wish->publish_user_id)
        {
            $error = '美愿基金只能打赏自己的愿望';
            return false;
        }
        if($wish->status == 0)
        {
            $error = '无效愿望，该愿望已被撤销';
            return false;
        }
        if($wish->finish_status > 1)
        {
            $error = '愿望已经结束';
            return false;
        }
        $cur_date = date('Y-m-d');
        if($cur_date > $wish->end_date)
        {
            $error = '愿望已经到期';
            return false;
        }
        return true;
    }

    /**
     * 增加浏览量，如果已经阅读过的返回true
     * @param $wish_id
     * @return bool
     */
    public static function AddWishViewCount($wish, $user_id,$phone_no, &$error)
    {
        $error = '';
        $wish_id = $wish->wish_id;
        $fileDir = \Yii::$app->getBasePath().'/web/store_data';
        if(!file_exists($fileDir))
        {
            mkdir($fileDir);
            chmod($fileDir,777);
        }
        $fileDir .= '/wish_view';
        if(!file_exists($fileDir))
        {
            mkdir($fileDir);
            chmod($fileDir, 777);
        }
        $file = $fileDir.'/wishview_'.$wish_id.'_'.strval($user_id).'.bin';
        //\Yii::getLogger()->log('view_file:'.$file,Logger::LEVEL_ERROR);
        if(file_exists($file))
        {
            //$error = '已经浏览过';
            return true;
        }
        if(file_exists($file))
        {
            return true;
        }
        $cont = $phone_no.' '.strval($user_id);
        $rst = file_put_contents($file, $cont);
        if($rst <= 0)
        {
            $error = '文件存储失败';
            return false;
        }

        if(!self::WishModify($wish,'view',$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 修改愿望，统一路径
     * @param $wish
     * @param $modify_type
     * @param $error
     * @param array $params
     */
    public static function WishModify($wish,$modify_type,&$error,$params=[])
    {
        $error = '';
        if(!($wish instanceof Wish))
        {
            $error = '不是愿望记录对象';
            return false;
        }
        $wishMofiyConfigFile = __DIR__.'/WishModifyActions/WishModifyConfig.php';
        if(!file_exists($wishMofiyConfigFile))
        {
            $error = '修改愿望配置文件不存在';
            \Yii::getLogger()->log($error.' file:'.$wishMofiyConfigFile,Logger::LEVEL_ERROR);
            return false;
        }
        $wishModifyConfig = require($wishMofiyConfigFile);
        if(!isset($wishModifyConfig[$modify_type]))
        {
            $error = '修改愿望类型不正确';
            \Yii::getLogger()->log($error.' modify_type:'.$modify_type,Logger::LEVEL_ERROR);
            return false;
        }
        $wishModifyClass = $wishModifyConfig[$modify_type];
        if(!class_exists($wishModifyClass))
        {
            $error = '修改愿望类不存在';
            \Yii::getLogger()->log($error.' class:'.$wishModifyClass,Logger::LEVEL_ERROR);
            return false;
        }
        $wish_id = $wish->wish_id;
        $phpLock = new PhpLock('wish_modify_'.strval($wish_id));
        $phpLock->lock();
        try
        {
            $modifyInstance = new $wishModifyClass;
            if(!$modifyInstance->WishModify($wish,$error,$params))
            {
                $phpLock->unlock();
                return false;
            }
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $phpLock->unlock();
            return false;
        }
        $phpLock->unlock();
        return true;
    }

    /**
     * 愿望提现审核
     * @param $user_id
     * @param $wish_id
     * @param $fee_rate
     * @param $error
     */
    public static function GenCheckForWishMoneyToBalance($user_id,$wish_id,$fee_rate,&$error)
    {
        $transActions= [];
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户不存在';
            return false;
        }
        if($user->centification_level === 0)
        {
            $error = '请先进行初级认证';
            return false;
        }
        $wish = WishUtil::GetWishRecordById($wish_id);
        if(!isset($wish))
        {
            $error = '愿望不存在';
            return false;
        }
        if(empty($fee_rate))
        {
            $error = '费率必须大于零';
            return false;
        }
        $checkRecord = BusinessCheckUtil::GetBusinessCheckModelNew(5,$wish_id,$user);
        $checkRecord->remark1 = strval($fee_rate);
        $transActions[] = new CheckRecordSaveForReward($checkRecord);
        $transActions[] = new WishSaveByTrans($wish,['modify_type'=>'to_balance_for_check','user_id'=>$user_id]);
        $msg = sprintf('您已成功提交申请:将愿望【%s】的金额转到余额；请耐心等待审核人员审核！',$wish->wish_name);
        $msgModel = MessageUtil::GetMsgNewModel(70,$msg,$user_id);
        $transActions[] = new MessageSaveForReward($msgModel);
        $businessLog = BusinessLogUtil::GetBusinessLogNew(264,$user);
        $businessMsg = sprintf('用户id【%s】昵称【%s】将已实现愿望【%s】进行提现，操作审核中。愿望id【%s】，发布人id【%s】，发布人【%s】',
            $user_id,$user->nick_name,$wish->wish_name,$wish_id,$wish->publish_user_id,$wish->publish_user_name);
        $businessLog->remark9 =$businessMsg;
        $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'愿望金额转余额业务日志存储失败']);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }
    /**
     * 新增愿望
     * @param $passParams
     * @param $user_id
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function AddWish($passParams,$user_id,&$wish_id,&$error)
    {
        //出图处理
        for($i =1; $i < 7; $i ++)
        {
            $keycnt = 'pic'.strval($i);
            $keysuffix = 'pic'.strval($i).'_suffix';
            $cnt = $passParams[$keycnt];
            $passParams[$keycnt] = '';
            if(isset($passParams[$keysuffix]))
            {
                unset($passParams[$keysuffix]);
            }
            if(strpos($cnt,'http://') === 0)
            {
                $passParams[$keycnt]  = $cnt;
            }
            //不是已经上传的图片不做处理
        }
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户不存在';
            return false;
        }
        $newWishInfo = WishNewStatisticUtil::GetNewModel(0);

        $model = new Wish();
        $model->attributes = $passParams;
        $model->red_packets_money = '0';
        $model->create_time = date('Y-m-d H:i:s',time());
        $model->collect_num = 0;
        $model->view_num = 0;
        $model->comment_num = 0;
        $model->reward_num = 0;
        $model->ready_reward_money = '0';
        $model->status = 1;
        $model->finish_status = 1;
        $model->is_official = 0;
        $model->hot_num = 0;
        $model->publish_user_id = $user_id;
        $model->publish_user_name = $user->nick_name;
        $model->min_reward = 0;
        $model->publish_user_phone = $user->phone_no;
        $model->is_finish = 1;
        $model->to_balance = 1;
        $model->back_status = 1;
        $model->back_count = 0;
        $model->back_money = 0.00;
        //$wish_type_id = $model->wish_type_id;
        /*$wishType = WishTypeUtil::GetWishTypeById($wish_type_id);
        if(!isset($wishType))
        {
            $error = '愿望类型不存在';
            return false;
        }
        $model->wish_type = $wishType->type_name;*/
        $userActive = UserActiveUtil::GetUserActiveByUserId($user_id);
        if(!isset($userActive))
        {
            $error = '用户活跃度信息不存在';
            return false;
        }

        //增加消息通知，如果字段 msg_use_ids
        $msgFriendsList = [];
        $msg_use_ids = $passParams['msg_use_ids'];
        if(!empty($msg_use_ids))
        {
            $uIds = explode(',',$msg_use_ids);
            if(!empty($uIds))
            {
                $content = sprintf('您的好友【%s】，发布了愿望【%s】，赶紧支持下吧！',$user->nick_name,$model->wish_name);
                foreach($uIds as $u_id)
                {
                    $msgTmp = MessageUtil::GetMsgNewModel(65,$content,$u_id);
                    $msgFriendsList[] = $msgTmp;
                }
            }
        }
        $hotExtend = new HotOrderExtend();
        $hotExtend->order_no = 1000;

        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            if(!$model->save())
            {
                \Yii::getLogger()->log(var_export($model->getErrors(), true),Logger::LEVEL_ERROR);
                $msg = $model->getFirstErrors();
                foreach($msg as $key => $value)
                {
                    $error = $value;
                }
                throw new Exception($error);
                //return false;
            }
            $userActive->wish_publish_count +=1;

            if(!UserActiveUtil::ModifyUseractive('add_wish',$userActive,$error))
            {
                \Yii::getLogger()->log(var_export($userActive->getErrors(), true),Logger::LEVEL_ERROR);
                $error = '用户活跃度保存失败';
                throw new Exception($error);
            }
            if(!empty($msgFriendsList))
            {
                foreach($msgFriendsList as $msgOne)
                {
                    if(!$msgOne->save())
                    {
                        \Yii::getLogger()->log(var_export($msgOne->getErrors(), true),Logger::LEVEL_ERROR);
                        $error = '发给朋友的消息存储失败';
                        throw new Exception($error);
                    }
                }
            }

            $newWishInfo->wish_id = $model->wish_id;
            if(!$newWishInfo->save())
            {
                \Yii::getLogger()->log(var_export($newWishInfo->getErrors(), true),Logger::LEVEL_ERROR);
                $error = '愿望最新排序记录存储失败';
                throw new Exception($error);
            }

            $hotExtend->wish_id = $model->wish_id;
            if(!$hotExtend->save())
            {
                \Yii::getLogger()->log(var_export($hotExtend->getErrors(), true),Logger::LEVEL_ERROR);
                $error = '愿望排行版扩张记录存储失败';
                throw new Exception($error);
            }

            $trans->commit();
        }
        catch(Exception $e)
        {
            $trans->rollBack();
            $error = $e->getMessage();
            return false;
        }
        $wish_id = $model->wish_id;
        return true;
    }

} 