<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/9
 * Time: 17:38
 */

namespace frontend\business;


use common\components\UsualFunForStringHelper;
use common\models\Comment;
use common\models\FriendsCircle;
use common\models\RedCircleReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceBySubRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RedDynamicRewardSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\TicketLivingMasterMoneyTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\TicketMyMoneyTrans;
use frontend\business\UserAccountBalanceActions\ModifyBalanceByTicketToCash;
use yii\db\Query;
use yii\log\Logger;

class DynamicUtil
{
    /**
     * 生成一条动态模型
     * @param $data
     * @return FriendsCircle
     */
    public static function GetDynamicModel($data)
    {
        $model = new FriendsCircle();
        $model->attributes = $data;
        $model->click_num = 0;
        $model->comment_num = 0;
        $model->check_num = 0;
        $model->status = 1;
        $model->create_time = date('Y-m-d H:i:s');

        return $model;
    }


    /**
     * 根据动态id 获取动态信息
     * @param $dynamic_id
     * @return FriendsCircle|null
     */
    public static function GetDynamicById($dynamic_id)
    {
        return FriendsCircle::findOne(['dynamic_id'=>$dynamic_id]);
    }

    /**
     * 创建评论模型
     * @param $data
     * @return bool
     */
    public static function NewCommentModel($data)
    {
        $model = new Comment();
        $model->dynamic_id = $data->dynamic_id;
        $model->user_id = $data->user_id;
        $model->content = $data->content;
        $model->to_user_id = $data->to_user_id;
        $model->status = $data->status;
        $model->create_time = date('Y-m-d H:i:s');

        return $model;
    }


    /**
     * 保存用户评论信息
     * @param $model
     * @param $error
     * @return bool
     */
    public static function SaveComment($model,&$error)
    {
        if(!($model instanceof Comment))
        {
            $error = '不是评论数据对象';
            return false;
        }

        if(!$model->save())
        {
            $error = '用户评论信息保存失败';
            \Yii::getLogger()->log($error. ' :'.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }

    /**
     * 保存动态信息
     * @param $model
     * @param $error
     * @return bool
     */
    public static function SaveDynamic($model,&$error)
    {
        if(!($model instanceof FriendsCircle))
        {
            $error = '不是动态信息对象';
            return false;
        }

        if(!$model->save())
        {
            $error = '动态信息更新失败';
            \Yii::getLogger()->log($error.' :'.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }

    /**
     * 处理红包动态打赏数据
     * @param $Dynamic
     * @param $date
     * @param $device_type
     * @param $error
     * @return bool
     */
    public static function CreateRedDynamic($Dynamic,$date,$device_type,&$error)
    {
        if($Dynamic->dynamic_type !== 2)
        {
            $error = '不是红包动态，无法打赏';
            return false;
        }

        if($Dynamic->status == 0)
        {
            $error = '该动态已经被删除';
            return false;
        }

        if($Dynamic->user_id == $date['user_id'])
        {
            $error = '无法对自己进行打赏';
            return false;
        }

        $reward_info = self::IsRewardByDynamic($Dynamic->dynamic_id,$date['user_id']);
        if(isset($reward_info))
        {
            $error = '已经打赏过，无需重复打赏';
            return false;
        }

        $balance = BalanceUtil::GetUserBalanceByUserId($date['user_id']);
        //\Yii::getLogger()->log('balance_user:'.var_export($date,true).'; balance_info: '.var_export($balance,true),Logger::LEVEL_ERROR);
        if($balance->bean_balance < $Dynamic->red_pic_money)
        {
            $error = '鲜花余额不足';
            return false;
        }

        $params = [
            'bean_num'=>$Dynamic->red_pic_money,
        ];
        //扣除打赏人用户余额
        $transActions[] = new ModifyBalanceBySubRealBean($balance,$params);
        $extend_params = [
            'unique_id'=>UsualFunForStringHelper::CreateGUID(),
            'op_value'=>$Dynamic->red_pic_money,
            'operate_type'=>21,
            'device_type'=>$device_type,
            'relate_id'=>'',
            'field'=>'bean_balance',
        ];
        //生成财务日志log
        $transActions[] = new CreateUserBalanceLogByTrans($balance,$extend_params);

        $to_balance = BalanceUtil::GetUserBalanceByUserId($Dynamic->user_id);
        $up_params = [
            'gift_value'=>$Dynamic->red_pic_money,
            'living_master_id'=>$Dynamic->user_id,
        ];
        //收到红包用户票处理
        $transActions[] = new TicketLivingMasterMoneyTrans($to_balance,$up_params);
        //生成财务日志log
        $extend_params['field'] = 'ticket_real_sum';
        $extend_params['operate_type'] = 22;
        $transActions[] = new CreateUserBalanceLogByTrans($to_balance,$extend_params);
        $extend_params['field'] = 'ticket_count_sum';
        $transActions[] = new CreateUserBalanceLogByTrans($to_balance,$extend_params);
        $extend_params['field'] = 'ticket_count';
        $transActions[] = new CreateUserBalanceLogByTrans($to_balance,$extend_params);
        $params = [
            'dynamic_id'=>$Dynamic->dynamic_id,
            'user_id'=>$date['user_id'],
            'to_user_id'=>$Dynamic->user_id,
            'reward_money'=>$Dynamic->red_pic_money,
        ];
        $ticket = [
            'user_id'=>$date['user_id'],
            'living_master_id'=>$Dynamic->user_id,
            'gift_value'=>$Dynamic->red_pic_money,
        ];
        if(!JobUtil::AddCustomJob('livingTicketBeanstalk','living_master_ticket',$ticket, $error))
        {
            return false;
        }
        $transActions[] = new RedDynamicRewardSaveByTrans($params);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$out ,$error))
        {
            return false;
        }
        $client = ClientUtil::GetClientById($date['user_id']);
        $data = [
            'key_word'=>'send_dynamic_im',
            'user_id'=>$date['user_id'],
            'nick_name'=>$date['nick_name'],
            'to_user_id'=>$Dynamic->user_id,
            'pic'=>(isset($client->icon_pic) ? $client->icon_pic : $client->pic),
            'content'=>'',
            'dynamic_id'=>$Dynamic->dynamic_id,
            'dynamic_pic'=>$Dynamic->pic,
            'create_time'=>date('Y-m-d H:i:s'),
            'reward_money'=>$Dynamic->red_pic_money,
            'type'=>'1',
        ];

        if(!JobUtil::AddImJob('tencent_im', $data, $error))
        {
            return false;
        }

        return true;
    }

    /**
     * 获取个人动态列表
     * @param $user_id
     * @param $page_no
     * @param $to_user_id
     * @param int $page_size
     * @return array
     */
    public static function GetDynamicListInfo($user_id,$to_user_id,$page_no,$page_size = 10)
    {
        if($page_size < 10)
        {
            $page_size = 10;
        }
        $condition = 'fc.user_id = :ud and fc.status = 1';
        $query = (new Query())
            ->select(['fc.user_id', 'nick_name', 'IFNULL(c.icon_pic,c.pic) as user_pic', 'fc.city', 'fc.dynamic_id', 'content', 'fc.pic', 'dim_pic' , 'click_num', 'comment_num', 'check_num', 'dynamic_type', 'red_pic_money as red_money', 'rcr.record_id', 'fc.create_time'])
            ->from('mb_friends_circle fc')
            ->innerJoin('mb_client c','fc.user_id = c.client_id')
            ->leftJoin('mb_red_circle_reward rcr','fc.dynamic_id = rcr.dynamic_id and rcr.user_id = :td')
            ->where($condition,[':ud'=>$user_id,':td'=>$to_user_id])
            ->orderBy('create_time desc')
            ->offset(($page_no - 1) * $page_size)
            ->limit($page_size)
            ->all();


        return $query;
    }

    /*
     * 根据用户ID获取用户的图片数量
     */
    public static function GetDynamicNum($user_id)
    {
        $query = (new Query())
            ->select('pic')
            ->from('mb_friends_circle')
            ->where('user_id=:uid and status = 1',[':uid'=>$user_id])
            ->all();

        foreach($query as $v)
        {
            $s = array_search($v, $query);
            $pic_num = $s;
        }
        if(empty($pic_num))
        {
            if(isset($pic_num))
            {
                return 1;
            }
            return 0;
        }
        return $pic_num+1;
    }


    /**
     * 根据用户id 和动态id 获取一条动态记录
     * @param $dynamic_id
     * @param $user_id
     * @return array|bool
     */
    public static function GetUserByDynamicInfo($dynamic_id)
    {
        $condition = 'dynamic_id = :md and fc.status = 1';
        $query = (new Query())
            ->select(['fc.user_id','nick_name','IFNULL(c.icon_pic,c.pic) as user_pic','fc.city','fc.dynamic_id','content','fc.pic','dim_pic','click_num','comment_num', 'check_num','dynamic_type','red_pic_money as red_money','fc.create_time'])
            ->from('mb_friends_circle fc')
            ->innerJoin('mb_client c','fc.user_id = c.client_id')
            ->where($condition,[':md'=>$dynamic_id])
            ->one();

        return $query;
    }
    /**
     * 根据动态id 获取评论列表
     * @param $data
     * @param $page_no
     * @param int $page_size
     * @return array
     */
    public static function GetCommentListInfo($data, $page_no, $page_size = 5)
    {
        if($page_size < 5)
        {
            $page_size = 5;
        }
        $condition = 'dynamic_id = :md and cm.status = 1';
        $query = (new Query())
            ->select(['user_id','nick_name', 'IFNULL(bc.icon_pic,bc.pic) as pic', '(select nick_name from mb_client where client_id = to_user_id) as to_user_name', 'content', 'cm.create_time'])
            ->from('mb_comment cm')
            ->leftJoin('mb_client bc','cm.user_id = bc.client_id')
            ->where($condition,[':md'=>$data->dynamic_id])
            ->orderBy('create_time desc')
            ->offset(($page_no - 1) * $page_size)
            ->limit($page_size)
            ->all();

        foreach($query as $list)
        {
            $s = array_search($list,$query);
            $list['is_return'] = 1;
            if(empty($list['to_user_name']))
            {
                $list['is_return'] = 0;
            }
            $query[$s] = $list;
        }
        return $query;
    }

    /**
     * 根据用户id 获取最新关注动态列表
     * @param $data
     * @param $page_no
     * @param int $page_size
     * @return array
     */
    public static function GetNewDynamicListInfo($data, $page_no, $page_size = 10)
    {
        if($page_size < 10)
        {
            $page_size = 10;
        }
        $condition = 'ba.user_id = :fd and fc.status = 1';
        $query = (new Query())
            ->select(['fc.dynamic_id', 'ba.friend_user_id as user_id', 'bc.nick_name', 'ifnull(bc.icon_pic,bc.pic) as user_pic', 'fc.city', 'content', 'fc.pic', 'fc.dim_pic', 'click_num', 'comment_num', 'check_num','red_pic_money as red_money' ,'dynamic_type', 'rcr.record_id', 'fc.create_time'])
            ->from('mb_attention ba')
            ->innerJoin('mb_client bc','ba.friend_user_id = bc.client_id')
            ->leftJoin('mb_friends_circle fc','fc.user_id = bc.client_id')
            ->leftJoin('mb_red_circle_reward rcr','fc.dynamic_id = rcr.dynamic_id and rcr.user_id = :ud')
            ->where($condition,[':fd'=>$data['user_id'],':ud'=>$data['user_id']])
            ->orderBy('fc.create_time desc')
            ->offset(($page_no - 1)* $page_size)
            ->limit($page_size)
            ->all();


        return $query;
    }

    /**
     * 更新动态点赞记录
     * @param $dynamic_id
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function UpdateDynamicClick($dynamic_id, &$error)
    {
        $sql = 'update mb_friends_circle set click_num = click_num + 1 WHERE dynamic_id = :md';
        $query = \Yii::$app->db->createCommand($sql,[
            ':md'=>$dynamic_id
        ])->execute();

        if($query <= 0)
        {
            $error = '更新动态点赞数失败';
            \Yii::getLogger()->log($error.' : dynamic_id:'.$dynamic_id.\Yii::$app->db->createCommand($sql,[
                    ':md'=>$dynamic_id
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }


    /**
     * 获取用户是否打赏动态记录
     * @param $dynamic_id
     * @param $user_id
     * @return $this
     */
    public static function IsRewardByDynamic($dynamic_id,$user_id)
    {
        return RedCircleReward::findOne(['dynamic_id'=>$dynamic_id,'user_id'=>$user_id]);
    }

    /**
     * @param $dynamic_id
     * 删除用户图片修改用户图片的状态
     */
    public static function UpdateDynamicStatus($dynamic_id)
    {
        $sql = 'update mb_friends_circle set status = 0 WHERE dynamic_id = :md';

        $query = \Yii::$app->db->createCommand($sql,[
           ':md' =>$dynamic_id
        ])->execute();

        if($query <= 0)
        {
            $error = '删除用户图片修改用户图片的状态失败';
            \Yii::getLogger()->log($error.' : dynamic_id:'.$dynamic_id.\Yii::$app->db->createCommand($sql,[
                    ':md'=>$dynamic_id
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;

    }

    /**
     * @param $user_id
     * @return bool
     * @throws \yii\db\Exception
     * 根据用户的id拿到用户的所有相册信息
     */
    public static function GetDynamicByUserId($user_id)
    {
        $query = (new Query() )
            ->select(['mfc.dynamic_id','mfc.click_num','mfc.comment_num','mfc.check_num','mfc.dynamic_type','mfc.red_pic_money','mfc.status','mc.client_no','mfc.content','mc.nick_name','mfc.create_time','mfc.pic','mfc.user_id'])
            ->from('mb_friends_circle mfc')
            ->innerJoin('mb_client mc','mc.client_id=mfc.user_id')
            ->where('mfc.user_id=:uid and mfc.status = 1',[':uid'=>$user_id])
            ->all();

        return  $query;
    }


//    public static function BatchUpdateDynamicStatus($dynamic_id,&$error)
//    {
//        if(!is_array($dynamic_id))
//        {
//            $error = '参数非数组';
//            return false;
//        }
//
//        if(empty($dynamic_id))
//        {
//            $error = '参数为空';
//            return false;
//        }
//
//        $sql = 'update mb_friends_circle set status = 0 WHERE dynamic_id = :md';
//
//        $params = [];
//        $i = 1;
//        $max = count($dynamic_id);
//        foreach($dynamic_id as $v)
//        {
//            $params[':md'.$i] = $v;
//            $sql .= sprintf('(:md%d)',$i);
//            if($i === $max)
//            {
//                $sql .= ';';
//            }
//            else
//            {
//                $sql .= ',';
//            }
//
//            $i++;
//        }
//
//        \Yii::getLogger()->log('sqlaa——————'.var_export($sql,true),Logger::LEVEL_ERROR);
//        $result = \Yii::$app->db->createCommand($sql,$params)->execute();
//        if($result <= 0)
//        {
//            \Yii::getLogger()->log('sql === '.\Yii::$app->db->createCommand($sql)->rawSql,Logger::LEVEL_ERROR);
//            $error = '审核删除相册图片失败';
//            return false;
//        }
//        return true;
//
//    }
} 