<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/28
 * Time: 9:33
 */

namespace frontend\business;


use backend\business\CloseLivingLogUtil;
use common\components\ClearCacheHelper;
use common\components\PhpLock;
use common\components\QiNiuUtil;
use common\components\SystemParamsUtil;
use common\components\tenxunlivingsdk\TimRestApi;
use common\components\UsualFunForStringHelper;
use common\models\ClientQiniu;
use common\models\CloseLivingLog;
use common\models\Living;
use common\models\LivingHot;
use common\models\LivingPasswrodTicket;
use common\models\LivingPersonnum;
use common\models\LivingPrivate;
use common\models\LivingTickets;
use common\models\StatisticLivingTime;
use common\models\SystemMessage;
use common\models\SystemParams;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateExperienceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ExperienceModifyByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FinishLivingSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\LivingMasterScoreLogSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\LivingMasterScoreSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\LivingMasterShareByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\LivingPersonNumModifyByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\LivingShareStatisticByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceBySubRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceBySubVirtualBean;
use yii\base\Exception;
use yii\db\Query;
use yii\log\Logger;

class LivingUtil
{

    /**
     * @param $unique_no
     */
    public static function GetLivingAndUserInfoByUniqueId($unique_no)
    {
        $query = new Query();
        $query->select(['ct.is_contract','ct.client_no','ct.is_centification','ifnull(li.living_before_id,\'\') as living_before_id','ifnull(li.op_unique_no,\'\') as op_unique_no','ifnull(li.status,\'\') as living_status','ct.client_id as living_master_id','ct.status','ifnull(li.living_id,\'\') as living_id','ifnull(cr.other_id,\'\') as other_id','li.living_type'])
            ->from('mb_client ct')
            ->leftJoin('mb_living li','ct.client_id = li.living_master_id')
            ->leftJoin('mb_chat_room cr','li.living_id = cr.living_id and cr.room_master_id=ct.client_id')
            ->where(['ct.unique_no'=>$unique_no]);
        return $query->one();
    }

    /**
     * 根据主播id获取直播记录
     * @param $user_id
     * @return Living|null
     */
    public static function GetLivingByMasterId($user_id)
    {
        return Living::findOne(['living_master_id'=>$user_id]);
    }

    /**
     * 进入或退出直播，修改热度
     * @param $data
     * @param $error
     */
    public static function LivingEnterOrQuit($data,&$error)
    {
        $living_id = $data['living_id'];
        $user_id = $data['user_id'];
        $op_type = $data['op_type'];
        $source_status = $data['source_status'];
        $living_master_id = $data['living_master_id'];
        if(empty($living_id) ||
            empty($user_id) ||
            empty($op_type)||
            empty($living_master_id)||
            !isset($source_status)
        )
        {

            $error = '参数不完整'.'living_id:'.$living_id.'user_id:'.$user_id.'op_type'.$op_type.'s:'.$source_status;
            return false;
        }
        if(!in_array($op_type,['enter','quit']))
        {
            $error = '操作类型错误';
            return false;
        }

        $lpm = new LivingPersonnum();// LivingPersonNumUtil::GetRecordByLivingId($living_id);
        $lpm->living_id = $living_id;
        if($source_status == 0)//未退出就进入，则不增加人数
        {
            $lp = new LivingPersonNumModifyByTrans($lpm,['op_type'=>$op_type]);
            if(!$lp->SaveRecordForTransaction($error,$out))
            {
                return false;
            }
//            \Yii::getLogger()->log('进入直播间source_status==:'.var_export($lpm,true),Logger::LEVEL_ERROR);
//            \Yii::getLogger()->flush(true);
        }
        //im发送人数通知
        $time = time();
        $end_time = \Yii::$app->cache->get('send_people_im_time_'.$living_id);
        $rst = $time - $end_time;
        if($rst > 2)
        {
            //\Yii::getLogger()->log('延迟秒数:'.$rst.'直播间id:'.$living_id,Logger::LEVEL_ERROR);
            //\Yii::getLogger()->flush(true);
            $extra = isset($data['extra']) ? $data['extra'] : null;
            if(!self::SendLivingNumToApp($lpm,$user_id,$living_master_id,$error,$extra))
            {
                \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
                \Yii::getLogger()->flush(true);
        }
            \Yii::$app->cache->set('send_people_im_time_'.$living_id,time(),60 * 2);
        }


        if($op_type === 'enter')//高等级用户发送欢迎字母
        {
            $extra = isset($data['extra']) ? $data['extra'] : null;
            if(!EnterRoomNoteUtil::SendEnterRoomNote($living_id,$user_id,$error, $extra))
            {
                \Yii::getLogger()->log('进入直播间异常：'.$error,Logger::LEVEL_ERROR);
            }
            //发送系统提示消息
            /*            if(!EnterRoomNoteUtil::SendEnterMessage($living_id,$user_id,$error))
                        {
                            \Yii::getLogger()->log('发送系统消息异常：'.$error,Logger::LEVEL_ERROR);
                        }*/
        }

        //更新直热度

        if(!JobUtil::AddHotJob('set_living_hot',['living_id' => $living_id],$error)){
            return false;
        }
        return true;
    }


    /**
     * 发送人数消息
     * @param $livingPerson
     * @param $user_id
     * @param $living_master_id
     * @param $error
     * @return bool
     */
    public static function SendLivingNumToApp($livingPerson,$user_id,$living_master_id,&$error,$extra=[])
    {
        if(!($livingPerson instanceof LivingPersonnum))
        {
            $error = '不是直播用户记录表';
            return false;
        }
        $imInfo = LivingUtil::GetLivingImInfo($living_master_id);
        if(empty($imInfo))
        {
            $error = '发送人数消息异常，直播间不存在或用户账户信息异常';
            \Yii::getLogger()->log($error.' living:'.$imInfo['living_id'],Logger::LEVEL_ERROR);
        }
        //$chatRoom = ChatGroupUtil::GetChatGroupByLivingId($livingPerson->living_id);
//        if(!isset($chatRoom))
//        {
//            $error = '发送人数消息异常，直播间不存在';
//            \Yii::getLogger()->log($error.' living_id:'.$chatRoom->living_id,Logger::LEVEL_ERROR);
//            return false;
//        }

//        $livingPerson = LivingPersonNumUtil::GetRecordByLivingId($livingPerson->living_id);
//        $userBalance = BalanceUtil::GetUserBalanceByUserId($living_master_id);//主播的票数
//        if(!isset($userBalance))
//        {
//            $error = '找不到用户账户信息';
//            \Yii::getLogger()->log($error.' user_id:'.$user_id,Logger::LEVEL_ERROR);
//            return false;
//        }
        $sendInfo = [
            'type'=>4,
            'ticket_count_sum'=>$imInfo['ticket_count_sum'],
            'person_count'=> $imInfo['person_count']
        ];
        $sv = json_encode($sendInfo);
        $error = $sv;
        $data = [
            'key_word'=>'send_people_im',
            'user_id'=>$user_id,
            'chat_room'=>$imInfo['other_id'],
            'sv'=>$sv,
            'living_id' => $imInfo['living_id'],
            'extra' => $extra
        ];
        if(!JobUtil::AddImJob('tencent_im',$data,$error))
        {
            \Yii::getLogger()->log('people job save error'.$error.' data_time'.date('Y-m-d H:i:s'),Logger::LEVEL_ERROR);
            return false;
        }
//        \Yii::getLogger()->log('进入直播间发送IM消息$data==:'.var_export($data,true),Logger::LEVEL_ERROR);
//        \Yii::getLogger()->flush(true);
        //延迟1秒发送人数消息
        /*if(!JobUtil::AddImDelayJob('tencent_im',$data,1,$error))
        {
            \Yii::getLogger()->log('people job save error'.$error.' data_time'.date('Y-m-d H:i:s'),Logger::LEVEL_ERROR);
            return false;
        }*/

        /*if(!TimRestApi::group_send_group_msg_custom($user_id,$chatRoom['other_id'],$sv,$error))
        {
            \Yii::getLogger()->log('发送人数im消息失败：'.'fail:'.$error.' date_time:'.date('Y-m-d H:i:s'),Logger::LEVEL_ERROR);
            \Yii::getLogger()->flush(true);
            return false;
        }*/
        return true;
    }

    /**
     * 根据直播时长id 获取直播时长id信息
     * @param $record_id
     * @return null|static
     */
    public static function GetStatisticLivingTimeById($record_id)
    {
        return StatisticLivingTime::findOne(['record_id'=>$record_id]);
    }
    /**
     * 根据直播id 获取直播信息
     * @param $living_id
     * @return null|static
     */
    public static function GetLivingById($living_id)
    {
        $living = Living::findOne(['living_id'=>$living_id]);
        return $living;
    }

    /**
     * 根据op_unique_no 获取直播信息
     * @param $op_unique_no
     * @return null|static
     */
    public static function GetLivingByOpUniqueNo($op_unique_no){
        $living = Living::findOne(['op_unique_no'=>$op_unique_no]);
        return $living;
    }

    /**
     * 根据直播id 获取用户直播信息
     * @param $livinId
     * @return array|false
     */
    public static function GetLivingInfo($livinId)
    {
        $query = (new Query())
            ->select(['cl.pic','li.living_title','li.app_id','getui_id','li.living_id','cl.client_id','cl.nick_name'])
            ->from('mb_living li')
            ->innerJoin('mb_client cl','cl.client_id=li.living_master_id')
            ->where('li.living_id=:lid',[':lid'=>$livinId])
            ->one();

        return $query;
    }

    /**
     * 根据直播id获取分享信息
     * @param $living_id
     * @param $shareInfo
     * @param $error
     * @return bool
     */
    public static function GetShareInfoForLiving($living_id,&$shareInfo,&$error)
    {
        $shareInfo = [];
        $livingInfo = self::GetLivingById($living_id);
        if(!isset($livingInfo))
        {
            $error = '直播信息不存在';
            return false;
        }
        if($livingInfo->status === 3)
        {
            $error = '该直播已被禁止';
            return false;
        }
        $clientInfo = self::GetLivingInfo($living_id);
        $configAry = \Yii::$app->params['default_param_api'];
        $key = $configAry['token'];

        $length = 40;
        $rand_str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $rand_str.=$strPol[rand(0,$max)];
        }
        $time = time();
        $sign = sha1('living_id='.$living_id.'&rand_str='.$rand_str.'&time='.time().'&key='.$key);
        $shareInfo['title'] = $livingInfo->living_title;
        $url = sprintf('http://%s/fuck/test2?living_id=%s&rand_str=%s&time=%s&sign=%s',
            $_SERVER['HTTP_HOST'],
            $living_id,
            $rand_str,
            $time,
            $sign
        );
        $shareInfo['content'] = $shareInfo['title'];
        $shareInfo['link'] = $url;
        $shareInfo['pic'] = $clientInfo['pic'];

        return true;
    }

    /**
     * 根据直播ID获取直播和直播室信息
     */
    public static function GetRoomInfoLiving($living_id){
        $result = (new Query())->select(['*'])->from('mb_living l')
            ->leftJoin('mb_chat_room r','l.living_id=r.living_id')
            ->where('l.living_id=:lid',[
                ':lid' => $living_id,
            ])->one();
        return $result;
    }


    /**
     * 获取最新直播前4张主播图片
     * @return array
     */
    public static function GetNewestLiving()
    {
        $query = (new Query())
            ->select(['bc.client_id as user_id','IFNULL(bc.middle_pic,bc.pic) as pic'])
            ->from('mb_livingmaster_hot lh')
            ->leftJoin('mb_time_livingmaster_ticketcount tlt','lh.livingmaster_id = tlt.livingmaster_id')
            ->leftJoin('mb_client bc','lh.livingmaster_id = bc.client_id')
            ->where('lh.hot_type = 1')
            ->groupBy('lh.livingmaster_id')
            ->orderBy('lh.hot_num DESC')
            ->limit(4)
            ->all();

        return $query;

    }

    /**
     * 获取最新直播信息
     * @param $page
     * @param int $page_size
     * @return array
     */
    public static function GetNewestLivingList($page,$user_id=-2,$page_size = 18)
    {
        $query = (new Query())
            ->select(['bc.is_contract','bl.living_pic_url','bl.pull_rtmp_url','bc.client_no','bc.client_id as user_id','nick_name','IFNULL(nullif(bc.middle_pic,\'\'),bc.pic) as pic','bl.city','bl.living_id','living_title','bl.device_type','cr.other_id as group_id',
                'mlp.person_count as living_num',
                'ca.level_no','ls.level_pic','ls.level_bg','ls.font_size','ls.color',
                'if(ifnull(lp.private_id,0) = 0,0,1) as private_status','if(ifnull(lpv.views_id,0)=0,0,1) as private_views','if(bl.living_type=3,lp.password,lpt.password) as password','if(ifnull(lpt.tikcet_id,0) = 0,0,1) as tikcet_status','if(ifnull(lptv.views_id,0)=0,0,1) as tikcet_views',
                'bl.game_name','bl.living_type','ifnull(bl.room_no,0) as room_no','ifnull(guess_num,\'-1\') as guess_num','ifnull(free_num,\'-1\') as over_guess_num','ifnull(lpt.tickets,\'0\') as tickets_num'
            ])
            ->from('mb_living bl')
            ->innerJoin('mb_living_personnum mlp','mlp.living_id=bl.living_id')
            ->innerJoin('mb_client bc','bl.living_master_id = bc.client_id')
            ->innerJoin('mb_client_active ca','ca.user_id = bc.client_id')
            ->innerJoin('mb_level ll','ll.level_id = ca.level_no')
            ->innerJoin('mb_level_stage ls','ls.level_stage = ll.level_max')
            ->innerJoin('mb_chat_room cr','bl.living_id = cr.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=bl.living_id and lp.living_before_id=bl.living_before_id and lp.living_master_id=bl.living_master_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id=lp.private_id and lpv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_living_passwrod_ticket lpt','lpt.living_id=bl.living_id and lpt.living_before_id=bl.living_before_id and lpt.living_master_id=bl.living_master_id')
            ->leftJoin('mb_living_passwrod_ticket_views lptv','lptv.tikcet_id=lpt.tikcet_id and lptv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_guess_record gr','gr.living_id=bl.living_id and gr.room_no=bl.room_no and gr.user_id=:uid',[':uid'=>$user_id])
            ->where('bl.status = 2')
            ->groupBy('bl.living_id')
            ->orderBy('bl.create_time DESC')
            ->offset(($page - 1)* $page_size)
            ->limit($page_size)
            ->all();
        return $query;
    }

    /**
     * 获取最新直播信息
     * @param $livingType [1,2] 或1
     * @param $page
     * @param int $user_id
     * @param int $page_size
     * @return array
     */
    public static function GetNewestLivingListByLivingType($livingType,$page,$user_id=-2,$page_size =18)
    {
        $query = (new Query())
            ->select(['bc.is_contract','bl.living_pic_url','bl.pull_rtmp_url','bc.client_no','bc.client_id as user_id','nick_name','IFNULL(nullif(bc.middle_pic,\'\'),bc.pic) as pic','bl.city','bl.living_id','living_title','bl.device_type','cr.other_id as group_id',
                'mlp.person_count as living_num',
                'ca.level_no','ls.level_pic','ls.level_bg','ls.font_size','ls.color',
                'if(ifnull(lp.private_id,0) = 0,0,1) as private_status','if(ifnull(lpv.views_id,0)=0,0,1) as private_views','if(bl.living_type=3,lp.password,lpt.password) as password','if(ifnull(lpt.tikcet_id,0) = 0,0,1) as tikcet_status','if(ifnull(lptv.views_id,0)=0,0,1) as tikcet_views',
                'bl.game_name','bl.living_type','ifnull(bl.room_no,0) as room_no','ifnull(guess_num,\'-1\') as guess_num','ifnull(free_num,\'-1\') as over_guess_num','ifnull(lpt.tickets,\'0\') as tickets_num'
            ])
            ->from('mb_living bl')
            ->innerJoin('mb_living_personnum mlp','mlp.living_id=bl.living_id')
            ->innerJoin('mb_client bc','bl.living_master_id = bc.client_id')
            ->innerJoin('mb_client_active ca','ca.user_id = bc.client_id')
            ->innerJoin('mb_level ll','ll.level_id = ca.level_no')
            ->innerJoin('mb_level_stage ls','ls.level_stage = ll.level_max')
            ->innerJoin('mb_chat_room cr','bl.living_id = cr.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=bl.living_id and lp.living_before_id=bl.living_before_id and lp.living_master_id=bl.living_master_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id=lp.private_id and lpv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_living_passwrod_ticket lpt','lpt.living_id=bl.living_id and lpt.living_before_id=bl.living_before_id and lpt.living_master_id=bl.living_master_id')
            ->leftJoin('mb_living_passwrod_ticket_views lptv','lptv.tikcet_id=lpt.tikcet_id and lptv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_guess_record gr','gr.living_id=bl.living_id and gr.room_no=bl.room_no and gr.user_id=:uid',[':uid'=>$user_id])
            ->andFilterWhere(['bl.status'=>2])
            ->andFilterWhere(['bl.living_type'=>$livingType])
            ->groupBy('bl.living_id')
            ->orderBy('bl.create_time DESC')
            ->offset(($page - 1)* $page_size)
            ->limit($page_size)
            ->all();
        return $query;
    }

    /**
     * 获取最新直播信息
     * @param $appID 应用程序id
     * @param $livingType [1,2] 或1
     * @param $page
     * @param int $user_id
     * @param int $page_size
     * @return array
     */
    public static function GetNewestLivingListByAppIDForLivingType($appID,$livingType,$page,$user_id=-2,$page_size =18)
    {
        $query = (new Query())
            ->select(['bc.is_contract','bl.living_pic_url','bl.pull_rtmp_url','bc.client_no','bc.client_id as user_id','nick_name','IFNULL(nullif(bc.middle_pic,\'\'),bc.pic) as pic','bl.city','bl.living_id','living_title','bl.device_type','cr.other_id as group_id',
                'mlp.person_count as living_num',
                'ca.level_no','ls.level_pic','ls.level_bg','ls.font_size','ls.color',
                'if(ifnull(lp.private_id,0) = 0,0,1) as private_status','if(ifnull(lpv.views_id,0)=0,0,1) as private_views','if(bl.living_type=3,lp.password,lpt.password) as password','if(ifnull(lpt.tikcet_id,0) = 0,0,1) as tikcet_status','if(ifnull(lptv.views_id,0)=0,0,1) as tikcet_views',
                'bl.game_name','bl.living_type','ifnull(bl.room_no,0) as room_no','ifnull(guess_num,\'-1\') as guess_num','ifnull(free_num,\'-1\') as over_guess_num','ifnull(lpt.tickets,\'0\') as tickets_num'
            ])
            ->from('mb_living bl')
            ->innerJoin('mb_living_personnum mlp','mlp.living_id=bl.living_id')
            ->innerJoin('mb_client bc','bl.living_master_id = bc.client_id')
            ->innerJoin('mb_client_active ca','ca.user_id = bc.client_id')
            ->innerJoin('mb_level ll','ll.level_id = ca.level_no')
            ->innerJoin('mb_level_stage ls','ls.level_stage = ll.level_max')
            ->innerJoin('mb_chat_room cr','bl.living_id = cr.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=bl.living_id and lp.living_before_id=bl.living_before_id and lp.living_master_id=bl.living_master_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id=lp.private_id and lpv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_living_passwrod_ticket lpt','lpt.living_id=bl.living_id and lpt.living_before_id=bl.living_before_id and lpt.living_master_id=bl.living_master_id')
            ->leftJoin('mb_living_passwrod_ticket_views lptv','lptv.tikcet_id=lpt.tikcet_id and lptv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_guess_record gr','gr.living_id=bl.living_id and gr.room_no=bl.room_no and gr.user_id=:uid',[':uid'=>$user_id])
            ->andFilterWhere(['bl.status'=>2])
            ->andFilterWhere(['bl.app_id'=>$appID])
            ->andFilterWhere(['bl.living_type'=>$livingType])
            ->orFilterWhere(['bl.status'=>2,'bl.living_type'=>5])
            ->groupBy('bl.living_id')
            ->orderBy('bl.create_time DESC')
            //->offset(($page - 1)* $page_size)
            // ->limit($page_size)
            ->all();
        return $query;
    }

    /**
     * 获取礼物列表
     * @return array
     */
    public static function GetGiftsList()
    {
        $query = (new Query())
            ->select(['gift_id','gift_name','pic','gift_value','special_effects','world_gift','remark1','lucky_gift'])
            ->from('mb_gift')
            ->where('remark2 = \'0\'')
            ->orderBy('order_no asc')
            ->all();

        return $query;
    }

    /**
     * 根据用户id ,获取用户总票数
     * @param $LoginInfo
     * @return array
     */
    public static function GetSumTicket($LoginInfo)
    {
        $query = (new Query())
            ->select(['ticket_num'])
            ->from('mb_time_livingmaster_ticketcount')
            ->where('livingmaster_id = :uid',[':uid'=>$LoginInfo['user_id']])
            ->all();

        return $query;
    }


    /**
     * 获取人气主播
     * @param $hot_type
     * @param $LoginInfo
     * @param $page
     * @param int $page_size
     * @return array
     */
    public static function GetHotLivingMaster($hot_type,$LoginInfo,$page,$page_size = 5)
    {
        $query = (new Query())
            ->select(['bc.pic','bc.nick_name','bc.sex','ba.user_id','(
            select count(friend_user_id) from mb_attention where user_id = lh.livingmaster_id) as funs_num',
                'if((select record_id from mb_attention where user_id = lh.livingmaster_id and friend_user_id = :ud),1,0) as is_attention'])
            ->from('mb_livingmaster_hot lh')
            ->innerJoin('mb_client bc','lh.livingmaster_id = bc.client_id')
            ->innerJoin('mb_attention ba','lh.livingmaster_id=ba.user_id')
            ->innerJoin('mb_time_livingmaster_ticketcount tlt','tlt.livingmaster_id = lh.livingmaster_id')
            ->where('tlt.hot_type = :tp',[':tp'=>$hot_type,':ud'=>$LoginInfo['user_id']])
            ->groupBy('lh.livingmaster_id')
            ->orderBy('lh.hot_num DESC')
            ->offset(($page - 1)* $page_size)
            ->limit($page_size)
            ->all();

        return $query;

        /*$sql = 'select u.pic,u.nick_name,u.sex,a.user_id,
(select count(friend_user_id) from mb_attention where user_id=h.livingmaster_id) as funs_num,
if((select record_id from mb_attention where
user_id=h.livingmaster_id and friend_user_id=:my_user_id),1,0) as is_attention
from mb_livingmaster_hot as h
inner join mb_client as u ON h.livingmaster_id= u.client_id
inner join mb_attention as a ON h.livingmaster_id=a.user_id
inner join mb_time_livingmaster_ticketcount as t ON t.livingmaster_id=h.livingmaster_id
where t.hot_type=:htype
group by h.livingmaster_id order by h.hot_num desc
limit :firstNum,:lastNum';*/
    }

    /**
     * 融云弹幕
     * @param $data
     * @param $user_id
     * @param $error
     */
    public static function RyDanmaku($data,$user_id,&$error)
    {
        //TODO:扣除豆、增加豆扣除日志表
        $living_id = $data['living_id'];
        $living = LivingUtil::GetLivingById($living_id);
        $userInfo = ClientUtil::getClientActive($user_id);
        
        if(!isset($living)) {
            $error = '直播不存在';
            return false;
        }
        if($living['status'] !== 2) {
            $error = '直播暂停或已结束';
            return false;
        }
        $balance = BalanceUtil::GetUserBalanceByUserId($user_id);
        if(!isset($balance)) {
            $error = '账户记录不存在，数据异常';
            return false;
        }

        if($balance['freeze_status'] == 2) { //TODO: 判断用户是否被冻结 状态为2表示已冻结
            $error = '账号冻结请联系客服';
            return false;
        }

        if ($userInfo['client_type'] != 2) {
            $danmakuBeanNum = SystemParamsUtil::GetSystemParam('bean_num_for_danmaku',true,'value1');
            $danmakuBeanNum = intval($danmakuBeanNum);
            if(empty($danmakuBeanNum)) {
                $error = '获取弹幕所需豆数参数异常';
                return false;
            }
            $hasVirtualBean = false;
            if($balance['virtual_bean_balance'] >= $danmakuBeanNum) {
                $hasVirtualBean = true;
            }
            if(!$hasVirtualBean && $balance['bean_balance'] < $danmakuBeanNum) {
                $error = '豆数不足';
                return false;
            }

            $transActions = [];
            if($hasVirtualBean)
            {
                $transActions[] = new ModifyBalanceBySubVirtualBean($balance,['vitrual_bean_num'=>$danmakuBeanNum]);
            }
            else
            {
                $transActions[] = new ModifyBalanceBySubRealBean($balance,['bean_num'=>$danmakuBeanNum]);
            }
            //增加日志
            //需要的参数 op_value 操作金额  operate_type 操作类型  unique_id 唯一id  device_type 设备类型 field 操作字段
            $logData = [
                'device_type'=>$data['device_type'],
                'operate_type'=>($hasVirtualBean ? 11 : 12),
                'op_value'=>$danmakuBeanNum,
                'unique_id'=>UsualFunForStringHelper::CreateGUID(),
                'field'=>($hasVirtualBean?'virtual_bean_balance':'bean_balance'),
                'relate_id'=>$living_id
            ];
            $transActions[] = new CreateUserBalanceLogByTrans($balance,$logData);
            if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error)) {
                return false;
            }
        } else {
            $danmakuBeanNum = 0;
        }
        $params = [
            'key_word'=>'send_danmu',
            'content'=>$data['text'],
            'living_id'=>$living_id,
            'user'=>[
                'id'=>$user_id,
                'name'=>$userInfo['nick_name'],
                'icon'=>$userInfo['pic'],
            ],
            'type'=>205,
            'extra'=>['is_super'=>intval($userInfo['client_type']),'level_no'=>intval($userInfo['level_no'])]
        ];
        if(!JobUtil::AddCustomJob('ImBeanstalk','tencent_im',$params,$error))
        {
            \Yii::error($error. ' im danmu队列job异常');
        }

        return ['price' => number_format($danmakuBeanNum, 0)];
    }

    /**
     * 弹幕
     * @param $data
     * @param $user_id
     * @param $error
     * @return bool
     */
    public static function Danmaku($data,$user_id,&$error)
    {
        //TODO:扣除豆、增加豆扣除日志表
        $living_id = $data['living_id'];
        $living = LivingUtil::GetLivingById($living_id);
        //$userInfo = ClientUtil::getClientActive($user_id);

        if(!isset($living)) {
            $error = '直播不存在';
            return false;
        }
        if($living['status'] !== 2) {
            $error = '直播暂停或已结束';
            return false;
        }
        $balance = BalanceUtil::GetUserBalanceByUserId($user_id);
        if(!isset($balance)) {
            $error = '账户记录不存在，数据异常';
            return false;
        }

        if($balance['freeze_status'] == 2) { //TODO: 判断用户是否被冻结 状态为2表示已冻结
            $error = '账号冻结请联系客服';
            return false;
        }
        $danmakuBeanNum = SystemParamsUtil::GetSystemParam('bean_num_for_danmaku',true,'value1');
        $danmakuBeanNum = intval($danmakuBeanNum);
        if(empty($danmakuBeanNum)) {
            $error = '获取弹幕所需豆数参数异常';
            return false;
        }
        $hasVirtualBean = false;
        if($balance['virtual_bean_balance'] >= $danmakuBeanNum) {
            $hasVirtualBean = true;
        }
        if(!$hasVirtualBean && $balance['bean_balance'] < $danmakuBeanNum) {
            $error = '豆数不足';
            return false;
        }

        $transActions = [];
        if($hasVirtualBean)
        {
            $transActions[] = new ModifyBalanceBySubVirtualBean($balance,['vitrual_bean_num'=>$danmakuBeanNum]);
        }
        else
        {
            $transActions[] = new ModifyBalanceBySubRealBean($balance,['bean_num'=>$danmakuBeanNum]);
        }
        //增加日志
        //需要的参数 op_value 操作金额  operate_type 操作类型  unique_id 唯一id  device_type 设备类型 field 操作字段
        $logData = [
            'device_type'=>$data['device_type'],
            'operate_type'=>($hasVirtualBean ? 11 : 12),
            'op_value'=>$danmakuBeanNum,
            'unique_id'=>UsualFunForStringHelper::CreateGUID(),
            'field'=>($hasVirtualBean?'virtual_bean_balance':'bean_balance'),
            'relate_id'=>$living_id
        ];
        $transActions[] = new CreateUserBalanceLogByTrans($balance,$logData);
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error)) {
            return false;
        }

        return true;
    }


    /**
     * 根据用户id ,获取等级信息
     * @param $user_id
     * @return array
     */
    public static function GetUserLevelInfo($user_id,&$levelInfo,&$error)
    {
        //用户等级信息
        $userInfo = (new Query())
            ->select(['user_id', 'level_id', 'bl.experience as level_stage','color','font_size','level_bg','ca.experience','ls.level_no','pic','ls.level_pic',])
            ->from('mb_client bc')
            ->innerJoin('mb_client_active ca','bc.client_id = ca.user_id')
            ->innerJoin('mb_level bl','ca.level_no=bl.level_id')
            ->leftJoin('mb_level_stage ls','bl.level_max = ls.level_stage')
            ->where('user_id=:ud',[':ud'=>$user_id])
            ->all();
        if(empty($userInfo))
        {
            $error = '获取用户等级信息参数错误';
            \Yii::getLogger()->log('level_user_id=:'.$user_id,Logger::LEVEL_ERROR);
            return false;
        }
        $levelInfo = [];
        foreach($userInfo as $Info)
        {
            $levelInfo = $Info;
        }

        if($levelInfo['level_id'] < 81)
        {
            $levelNum = intval($levelInfo['level_no']) + 1;
            //下个等级段图标
            $query = (new Query())
                ->select(['level_pic','level_bg'])
                ->from('mb_level_stage')
                ->where('level_no = :ld',[':ld'=>$levelNum])
                ->all();
            $nextPic = [];
            foreach($query as $q)
            {
                $nextPic = $q;
            }
            if(!isset($query))
            {
                $error = '等级信息不存在';
                \Yii::getLogger()->log($error.' '.var_export($query,true),Logger::LEVEL_ERROR);
                return false;
            }
            $levelInfo['next_level_pic'] = $nextPic['level_pic'];//下个成就图片
            $levelInfo['next_level_bg'] = $nextPic['level_bg']; //下个图片背景
        }
        if($levelInfo['level_id'] < 200)
        {
            $levelId = intval($levelInfo['level_id']) + 1;
            $sql = (new Query())
                ->select(['level_id','experience'])
                ->from('mb_level')
                ->where('level_id =:ld',[':ld'=>$levelId])
                ->all();
            $t = [];
            foreach($sql as $s)
            {
                $t = $s;
            }
            $levelInfo['up_level'] = intval($t['experience']) - intval($levelInfo['level_stage']);//升级需要经验
            $levelInfo['next_level'] = $t['level_id'];//下个等级
            $levelInfo['differ'] = $levelInfo['up_level'] - ($levelInfo['experience'] - $levelInfo['level_stage']);
        }

        $levelInfo['exp'] = $levelInfo['experience'] - $levelInfo['level_stage'];
        unset($levelInfo['level_stage']);
        unset($levelInfo['level_no']);

        return true;
    }

    /**
     * 根据直播ID获取主播详细信息
     */
    public static function GetUserInfo($living_id){
        $query = new Query();
        $user_info = $query->select(['l.living_id','c.client_id','c.unique_no','c.client_no','c.age', 'c.nick_name',
            'c.pic','c.main_pic','c.sign_name','c.sex','if(l.status=2,1,0) as flag', 'l.living_master_id','l.pull_rtmp_url','l.pull_hls_url',
            'a.active_id','a.attention_num', 'a.funs_num','a.experience','a.level_no','l.pull_http_url'])
            ->from('mb_living l')
            ->leftJoin('mb_client c','c.client_id=living_master_id')
            ->leftJoin('mb_client_active a','c.client_id=user_id')
            ->where('l.living_id=:lid',[':lid'=>$living_id])
            ->one();
        return $user_info;
    }

    /**
     * 获取断线直播
     */
    public static function GetOffLineLiving()
    {
        $qurey = new Query();
        $qurey->from('mb_living')->select(['living_id','living_master_id','finish_time'])
            //->innerJoin('mb_chat_room cr','li.living_id = cr.living_id')
            ->where(['and','status=2',['<','finish_time',date('Y-m-d H:i:s',strtotime('-1 min'))]])
            ->limit(500);
        return $qurey->all();
    }

    /**
     * 判断直播是否存在
     * @param $living_id
     */
    public static function CheckLivingIsExist($living_id){
        $living_info = Living::findOne('living_id=:lid',[':lid'=>$living_id]);
        if(!$living_info){
            return false;
        }
        return true;
    }
    /**
     * 设置经验已转换
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SetExpirenceYes($living_id){
        $sql = 'update mb_living set is_to_expirence=1 where living_id=:lid';
        $result = \Yii::$app->db->createCommand($sql,[
            ':lid' => $living_id
        ])->execute();
        if($result === false){
            return false;
        }

        return true;
    }

    /**
     * 获取当前直播统计信息，服务于直播结束信息
     * @param $living_id
     * @return array|bool
     */
    public static function GetFinishLivingInfo($living_id)
    {
        $query = new Query();
        $result = $query->select(['l.device_type','l.living_before_id','l.create_time','l.living_master_id','l.status','l.living_id','l.heart_count','p.person_count_total','t.tickets_num','t.tickets_real_num','l.living_time','g.goods_num','h.hot_num'])
            ->from('mb_living l')
            ->innerJoin('mb_living_tickets t','t.living_id=l.living_id')
            ->innerJoin('mb_living_goods g','g.living_id=l.living_id')
            ->innerJoin('mb_living_hot h','h.living_id=l.living_id')
            ->innerJoin('mb_living_personnum p','p.living_id=l.living_id')
            ->where('l.living_id=:lid',[
                ':lid' => $living_id
            ])
            ->one();
        return $result;
    }

    /**
     * 直播结束操作
     * @param $living_id // 直播间id
     * @param $outinfomain //返回值
     * @param $error
     * @return bool
     */
    public static function SetFinishLiving($living_id,&$outinfomain,&$error)
    {
        $result = self::GetFinishLivingInfo($living_id); //TODO:获取直播间统计信息
        if($result['status'] == 0)
        {
            $outinfomain['attend_user_count'] = $result['person_count_total'];
            $outinfomain['tickets_num'] = $result['tickets_num'];
            $outinfomain['living_time'] = $result['living_time'];
            $outinfomain['living_status'] = 0;
            return true;
        }
        $finish_second_time = time()-strtotime($result['create_time']);
        $finish_mins_time = intval($finish_second_time/60);
        $finish_hours_time = intval($finish_second_time/3600);
        $living_time = UsualFunForStringHelper::GetHHMMSSBySeconds($finish_second_time);
        $params = [
            'living_time' => $living_time,
            'living_id' => $living_id,
            'goods_num' => $result['goods_num'],
            'tickets_num' => $result['tickets_num'],
            'hot_num' => $result['hot_num'],
            'person_count_total' => $result['person_count_total'],
            'living_before_id' => $result['living_before_id'],
            'living_master_id' => $result['living_master_id'],
            'tickets_real_num' => $result['tickets_real_num'],
            'living_second_time' => $finish_second_time
        ];

        $transActions[] = new FinishLivingSaveForReward($params);

        if(($finish_hours_time >0 ) || ($finish_mins_time > 0)){
            $clentActive = ClientActiveUtil::GetClientActiveInfoByUserId($result['living_master_id']);
            $to_experience = SystemParamsUtil::GetSystemParam('living_master_min_experience',false,'value1'); //豆与经验转化率
            $experience_num = $finish_mins_time*$to_experience;    //直播经验值  直播每分钟30点经验；
            $transActions[] = new ExperienceModifyByTrans($clentActive,['experience_num'=>$experience_num]);
            //经验日志写入
            $extend_params = [
                'device_type' => $result['device_type'],
                'user_id' => $result['living_master_id'],
                'source_type' => 3, //直播
                'living_before_id' => $result['living_before_id'],
                'change_rate' => $to_experience,
                'experience' => $experience_num,
                'create_time' => date('Y-m-d H:i:s',time()),
                'starttime' => $finish_mins_time,
                'endtime' => $finish_second_time,
                'owner' => 1,
                'is_to_expirence' => 1,
                'living_id' => $living_id

            ];
            $transActions[] = new CreateExperienceLogByTrans($clentActive,$extend_params);
        }
        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo, $error))
        {
            return false;
        }

        //删除私密直播用户信息队列
//        $sum_data = [
//            'living_id' => $living_id,
//            'living_before_id' => $result['living_before_id'],
//        ];
//        if(!JobUtil::AddCustomJob('DeleteLivingPrivateBeanstalk','delete_living_private',$sum_data,$error))
//        {
//            return false;
//        }
        //\Yii::$app->cache->delete('up_game_info_'.$living_id);  //退出直播间，清除记录前一局游戏的缓存信息
        $outinfomain['attend_user_count'] = $result['person_count_total'];
        $outinfomain['tickets_num'] = $result['tickets_num'];
        $outinfomain['living_time'] = $living_time;
        $outinfomain['living_status'] = 1;

        return true;
    }

    /**
     * 根据用户id 获取直播间信息
     * @param $user_id
     * @return null|static
     */
    public static function GetLivingUserInfo($user_id)
    {
        return Living::findOne(['living_master_id'=>$user_id]);
    }

    /**
     * 根据直播ID获取WEB直播页面所需要的主播和观众信息
     * @param $living_id
     */
    public static function GetLivingMasterInfo($living_id){
        $query = (new Query())
            ->select(['ifnull(c.main_pic,c.pic) as pic','nick_name','level_no','person_count_total','tickets_num','room_id','c.client_id','c.client_no'])
            ->from('mb_living l')
            ->innerjoin('mb_client c','c.client_id=l.living_master_id')
            ->innerjoin('mb_living_personnum p','p.living_id=l.living_id')
            ->innerjoin('mb_living_tickets t','t.living_id=l.living_id')
            ->innerjoin('mb_client_active ca','ca.user_id=l.living_master_id')
            ->innerjoin('mb_chat_room r','r.living_id=l.living_id')
            ->where('l.living_id=:lid',[':lid'=>$living_id])
            ->one();
        return $query;
    }

    /**
     * 根据群ID获取观众名称，头像
     * @param $group_id
     * @return array
     */
    public static function GetLivingPersonInfo($room_id){
        $query = (new Query())
            ->select(['c.nick_name','ifnull(c.icon_pic,c.pic) as pic'])
            ->from('mb_chat_room_member rm')
            ->innerjoin('mb_client c','c.client_id=rm.user_id')
            ->where('group_id =:gid',[':gid'=>$room_id])
            ->orderBy('c.client_id ASC')
            ->limit(4)
            ->all();
        return $query;
    }


    /**
     * 根据直播ID 获取信息
     * @param $living_id
     * @return array|bool
     */
    public static function GetLivingClientInfo($living_id){
        $query = (new Query())
            ->select(['ifnull(c.icon_pic,c.pic) as pic','l.living_title'])
            ->from('mb_living l')
            ->innerJoin('mb_client c','c.client_id=l.living_master_id')
            ->where('l.living_id=:lid',[
                ':lid' => $living_id
            ])
            ->One();
        return $query;
    }


    /**
     * 获取某个直播的在线人数
     * @param $living_id
     * @return array|bool
     */
    public static function GetLivingOnlinePerson($living_id)
    {
        $query = (new Query())
            ->select(['count(record_id) as person_count'])
            ->from('mb_chat_room_member rm')
            ->innerJoin('mb_chat_room cm','cm.room_id=rm.group_id')
            ->innerJoin('mb_living li','li.living_id=cm.living_id')
            ->where('rm.owner!=1 and rm.status=1 and li.living_id=:lid',[
                ':lid' => $living_id
            ])
            ->one();
        return $query;
    }

    /**
     * 通过直播ID获得mb_living_tickets表信息
     * @param $living_id
     * @return null|static
     */
    public static function GetLivingLivingTickets($living_id)
    {
        return LivingTickets::findOne(['living_id'=>$living_id]);
    }

    /**
     * 保存直播信息
     * @param $living
     * @param $error
     */
    public static function SaveLiving($living,&$error)
    {
        if(!($living instanceof Living))
        {
            $error = '不是直播间记录';
            return false;
        }
        if(!$living->save())
        {
            $error = '直播间记录保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($living->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 保存直播时长信息
     * @param $living
     * @param $error
     * @return bool
     */
    public static function SaveStatisticLivingTime($living,&$error)
    {
        if(!($living instanceof StatisticLivingTime))
        {
            $error = '不是直播时长记录';
            return false;
        }
        if(!$living->save())
        {
            $error = '直播间记录保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($living->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 保存热门直播
     * @param $living
     * @param $error
     */
    public static function SaveHotLiving($living,&$error)
    {
        if(!($living instanceof LivingHot))
        {
            $error = '不是热门直播记录';
            return false;
        }
        if(!$living->save())
        {
            $error = '热门直播记录保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($living->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    /**
     * 根据用户id 获取本月直播时长
     * @param $client_id
     * @param $out
     * @param $error
     * @return bool
     */
    public static function LivingTimeStatistics($client_id,&$out,&$error)
    {
        $start_time = date('Y-m-d',strtotime(date('Y-m', time()).'-01 00:00:00'));
        $end_time = date('Y-m-d');
        $query = (new Query())
            ->select(['SUM(living_second_time) as s'])
            ->from('mb_living_statistics')
            ->where('living_master_id = :lmd AND DATE_FORMAT(finish_time,\'%Y-%m-%d\') >= :st AND DATE_FORMAT(finish_time,\'%Y-%m-%d\') < :et',[':lmd'=>$client_id,':st'=>$start_time,':et'=>$end_time])
            ->one();

        if($query === false)
        {
            $error = '时长信息不存在';
            \Yii::getLogger()->log($error.' : '.var_export($query,true),Logger::LEVEL_ERROR);
            return false;
        }

        $out = UsualFunForStringHelper::GetHHMMBySeconds($query['s']);
        return true;
    }

    /**
     * 设置禁用用户，直接结束直播，并发送IM消息
     * @param $living_id
     * @param $finishInfo
     * @param $living_master_id
     * @param $other_id
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public static function SetBanClientFinishLiving($living_id,$finishInfo,$living_master_id,$other_id,&$outInfo,&$error)
    {
        if(!LivingUtil::SetFinishLiving($living_id,$finishInfo,$error))
        {
            \Yii::getLogger()->log('结束直播异常：'.$error.' living_id:'.$living_id, Logger::LEVEL_ERROR);
            $error = '结束直播异常';
            return false;
        }
        if($finishInfo['living_status'] == 0)
        {
            return true;
        }
        //发送自定义消息到腾讯
        $sendInfo = [
            'type'=>7,
            'attend_user_count'=>$finishInfo['attend_user_count'],
            'tickets_num'=>sprintf('%d',$finishInfo['tickets_num']),
            'living_time'=>$finishInfo['living_time']
        ];
        $sv = json_encode($sendInfo);
        if(!TimRestApi::group_send_group_msg_custom($living_master_id,$other_id,$sv,$error))
        {
            $outInfo .= 'fail:'.$error.' date_time:'.date('Y-m-d H:i:s')."\n";
            \Yii::getLogger()->log('发送im结束直播消息失败：'.'fail:'.$error.' date_time:'.date('Y-m-d H:i:s').'   $living_master_id==:'.$living_master_id.'    group_id==:'.$other_id,Logger::LEVEL_ERROR);
        }

        return true;
    }

    /**
     * 设置禁用用户，直接结束直播，并发送IM消息 for 封播
     * @param $living_id
     * @param $finishInfo
     * @param $living_master_id
     * @param $other_id
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public static function SetBanClientFinishLivingToStopLiving($living_id,$finishInfo,$living_master_id,$other_id,&$outInfo,&$error)
    {
        if(!LivingUtil::SetFinishLiving($living_id,$finishInfo,$error))
        {
            \Yii::getLogger()->log('结束直播异常：'.$error.' living_id:'.$living_id, Logger::LEVEL_ERROR);
            $error = '结束直播异常';
            return false;
        }
        if($finishInfo['living_status'] == 0)
        {
            return true;
        }
        //发送自定义消息到腾讯
        $sendInfo1 = [
            'type'=>7,
            'attend_user_count'=>$finishInfo['attend_user_count'],
            'tickets_num'=>sprintf('%d',$finishInfo['tickets_num']),
            'living_time'=>$finishInfo['living_time']
        ];

        $sendInfo2 = [
            'type'=>510,
            'attend_user_count'=>$finishInfo['attend_user_count'],
            'tickets_num'=>sprintf('%d',$finishInfo['tickets_num']),
            'living_time'=>$finishInfo['living_time']
        ];

        $sv1 = json_encode($sendInfo1);
        if(!TimRestApi::group_send_group_msg_custom($living_master_id,$other_id,$sv1,$error))
        {
            $outInfo .= 'fail:'.$error.' date_time:'.date('Y-m-d H:i:s')."\n";
            \Yii::getLogger()->log('发送im结束直播消息失败：'.'fail:'.$error.' date_time:'.date('Y-m-d H:i:s').'   $living_master_id==:'.$living_master_id.'    group_id==:'.$other_id,Logger::LEVEL_ERROR);

        }

        $sv2 = json_encode($sendInfo2);
        if(!TimRestApi::group_send_group_msg_custom($living_master_id,$other_id,$sv2,$error))
        {
            $outInfo .= 'fail:'.$error.' date_time:'.date('Y-m-d H:i:s')."\n";
            \Yii::getLogger()->log('发送im结束直播消息失败：'.'fail:'.$error.' date_time:'.date('Y-m-d H:i:s').'   $living_master_id==:'.$living_master_id.'    group_id==:'.$other_id,Logger::LEVEL_ERROR);

        }

        return true;
    }

    /**
     * 根据living_id获取直播基本信息
     */
    public static function GetClientLivingInfo($living_id)
    {
        $query = (new Query())
            ->from('mb_living li')
            ->select(['li.status','li.living_id','living_master_id','li.living_before_id','cr.other_id','finish_time','li.living_type','li.room_no','mp.person_count','li.limit_num'])
            ->innerJoin('mb_chat_room cr','li.living_id = cr.living_id')
            ->innerJoin('mb_living_personnum mp','mp.living_id = li.living_id')
            ->where('li.living_id=:lmid',[':lmid'=>$living_id])
            ->one();
        return $query;
    }

    public static function GetLivingPersonnum($living_id)
    {
        $query = LivingPersonnum::findOne(['living_id'=>$living_id]);
        return $query;
    }

    /**
     * 根据living_master_id获取直播基本信息
     */
    public static function GetClientLivingInfoByLivingMasterId($living_master_id)
    {
        $query = (new Query())
            ->from('mb_living li')->select(['li.status','li.living_id','living_master_id','cr.other_id','finish_time'])
            ->innerJoin('mb_chat_room cr','li.living_id = cr.living_id')
            ->where('li.living_master_id=:lmid',[':lmid'=>$living_master_id])
            ->one();
        return $query;
    }


    /**
     * 获取送礼物，主播、直播信息
     * @param $living_id
     * @return array|bool
     */
    public static function GetSendGiftLivingInfo($living_id)
    {
        $query = (new Query())
            ->select(['cla.level_no','cl.nick_name','li.op_unique_no','li.device_type','li.living_id','li.living_master_id','li.living_before_id','ba.bean_balance','ba.virtual_bean_balance','ba.user_id','ba.balance_id','ba.ticket_count_sum','rm.room_id','rm.other_id','lit.living_tickets_id','lit.tickets_num'])
            ->from('mb_living li')
            ->innerJoin('mb_chat_room rm','rm.living_id=li.living_id')
            ->innerJoin('mb_balance ba','ba.user_id=li.living_master_id')
            ->innerJoin('mb_living_tickets lit','lit.living_id=li.living_id')
            ->innerJoin('mb_client cl','cl.client_id=li.living_master_id')
            ->innerJoin('mb_client_active cla','cla.user_id=li.living_master_id')
            ->where('li.living_id=:lid',[':lid'=>$living_id])
            ->one();
        return $query;
    }

    /**
     * 获取七牛直播信息
     * @param $user_id
     */
    public static function GetQiNiuLivingInfo($user_id,$unique_no,&$error)
    {
        $key = 'qiniu_living_'.strval($user_id);
        $tmpStr = \Yii::$app->cache->get($key);
        if($tmpStr === false)
        {
            $qiniuInfo = ClientQiNiuUtil::GetQiNiuInfoByClientId($user_id);
            if(!isset($qiniuInfo))
            {
                //获取七牛信息
                $is_test = \Yii::$app->params['is_test'];
                $title = ($is_test === '1'?'livingtest'.$unique_no: 'livingmibo'.$unique_no);//七牛直播流昵称，唯一
                $qnInfo = QiNiuUtil::CreateStream($title,$error);
                if($qnInfo === false)
                {
                    \Yii::getLogger()->log('获取七牛云直播信息异常：'.$error,Logger::LEVEL_ERROR);
                    $error = '获取七牛云直播信息异常';
                    return false;
                }
                $tmpStr = json_encode($qnInfo);
                $clientQiuNiu = new ClientQiniu();
                $clientQiuNiu->user_id = $user_id;
                $clientQiuNiu->qiniu_info = $tmpStr;
                if(!$clientQiuNiu->save())
                {
                    \Yii::getLogger()->log('保存七牛信息失败:'.var_export($clientQiuNiu->getErrors(),true),Logger::LEVEL_ERROR);
                    $error = '保存七牛信息失败';
                    return false;
                }
            }
            else
            {
                $tmpStr = $qiniuInfo->qiniu_info;
            }
            \Yii::$app->cache->set($key,$tmpStr);
        }
        return $tmpStr;
    }

    /**
     * 获取发送im房间信息
     * @param $user_id
     * @return array|bool
     */
    public static function GetLivingImInfo($user_id)
    {
        $query = (new Query())
            ->select(['ml.living_id','ticket_count_sum','person_count','other_id'])
            ->from('mb_living ml')
            ->innerJoin('mb_chat_room mcr','ml.living_id = mcr.living_id')
            ->innerJoin('mb_living_personnum mlp','mcr.living_id = mlp.living_id')
            ->innerJoin('mb_balance mb','ml.living_master_id = mb.user_id')
            ->where('ml.living_master_id = :ud',[':ud'=>$user_id])
            ->one();

        return $query;
    }

    /**
     * 获取正在进行的活动列表
     * @return array
     */
    public static function GetScoreActivity()
    {
        $query = (new Query())
            ->select(['activity_id','title','start_time','end_time','activity_status','template_id'])
            ->from(['mb_activity_giftscore'])
            ->where('start_time <=:stime and end_time>=:endtime and activity_status=1',[':endtime'=>date('Y-m-d'),':stime'=>date('Y-m-d')])
            ->all();
        return $query;
    }

    /**
     * 获取正在进行的活动礼物信息
     * @param $gift_id
     * @return array|bool
     */
    public static function GetScoreGifts()
    {
        $query = (new Query())
            ->select(['gf.gift_id','gf.gift_name','sc.score'])
            ->from(['mb_gift_score sc'])
            ->innerJoin('mb_gift gf','gf.gift_id=sc.gift_id')
            ->all();
        return $query;
    }

    /**
     * 设置活动缓存
     * @param array $outData
     * @param $error
     * @return bool
     */
    public static function SetCacheScoreActivity(&$outData = [],&$error)
    {
        $activity_list = self::GetScoreActivity();
        $activity_list_json = json_encode($activity_list);
        $activity_info = \Yii::$app->cache->set('get_score_activity',$activity_list_json);
        $outData = $activity_list_json;
        if(!$activity_info)
        {
            \Yii::getLogger()->log('活动列表缓存写入失败   get_score_activity==:'.var_export($activity_list,true),Logger::LEVEL_ERROR);
            $error = '活动列表获取失败';
            return false;
        }
        return true;
    }

    /**
     * 设置活动礼物缓存
     * @param array $outData
     * @param $error
     * @return bool
     */
    public static function SetCacheScoreGift(&$outData = [],&$error)
    {
        $gift_list = self::GetScoreGifts();
        if(empty($gift_list))
        {
            $gift_list_json = json_encode($gift_list);
            $gift_info = \Yii::$app->cache->set('get_score_gifts',$gift_list_json);
            $outData = $gift_list_json;
            if(!$gift_info)
            {
                \Yii::getLogger()->log('活动礼物列表缓存写入失败   get_score_gifts==:'.var_export($gift_list,true),Logger::LEVEL_ERROR);
                $error = '活动礼物列表获取失败';
                return false;
            }
            return true;
        }

        $new_gift_arr = [];
        foreach($gift_list as $gift)
        {
            $new_gift_arr[$gift['gift_id']] = $gift;    //处理礼物数据，将礼物id换成数组key
        }

        $gift_list_json = json_encode($new_gift_arr);
        $gift_info = \Yii::$app->cache->set('get_score_gifts',$gift_list_json);
        $outData = $gift_list_json;
        if(!$gift_info)
        {
            \Yii::getLogger()->log('活动礼物列表缓存写入失败   get_score_gifts==:'.var_export($new_gift_arr,true),Logger::LEVEL_ERROR);
            $error = '活动礼物列表获取失败';
            return false;
        }
        return true;
    }

    /**
     * 主播积分累加
     * @param $living_master_id
     * @param $gift_id
     * @param $error
     * @return bool
     */
    public static function SetLivingMasterIntegral($living_master_id,$gift_id,$send_user_id,&$error)
    {
//        $time1 = microtime(true);
        $get_score_gifts = \Yii::$app->cache->get('get_score_gifts');  //积分礼物缓存列表信息
        $get_score_activity = \Yii::$app->cache->get('get_score_activity');  //积分活动缓存列表信息
        if($get_score_activity === false)
        {
            $phpLock = new PhpLock('get_score_activity');
            $phpLock->lock();
            $get_score_activity = \Yii::$app->cache->get('get_score_activity');
            if($get_score_activity === false)
            {
                if(!self::SetCacheScoreActivity($outData,$error))   //设置活动缓存信息
                {
                    $phpLock->unlock();
                    return false;
                }
                $get_score_activity = $outData;
            }
            $phpLock->unlock();
        }

        $get_score_activity = json_decode($get_score_activity,true);
        if(empty($get_score_activity))
        {
            return true;
        }

        $time = date('Y-m-d');
        foreach($get_score_activity as $key=>$activity) {
            if ((($activity['start_time'] > $time) || ($activity['activity_status'] == 2)) || (($activity['end_time'] < $time) || ($activity['activity_status'] == 0))) {
                unset($get_score_activity[$key]);
            }
        }
        if(empty($get_score_activity))
        {
            return true;
        }

        if($get_score_gifts === false)
        {
            $phpLock = new PhpLock('get_score_gifts');
            $phpLock->lock();
            $get_score_gifts = \Yii::$app->cache->get('get_score_gifts');
            if($get_score_gifts === false)
            {
                if(!self::SetCacheScoreGift($outData,$error))   //设置礼物缓存信息
                {
                    $phpLock->unlock();
                    return false;
                }
                $get_score_gifts = $outData;
            }
            $phpLock->unlock();

        }

        $get_score_gifts = json_decode($get_score_gifts,true);
        if(!array_key_exists($gift_id,$get_score_gifts))
        {
            //\Yii::getLogger()->log('活动礼物不存在  gift_id===:'.$gift_id,Logger::LEVEL_ERROR);
            return true;
        }

        $transActions = [];
        foreach($get_score_activity as $activity)
        {
            if(($activity['start_time'] > $time) || ($activity['activity_status'] == 2))
            {
                $error = '活动还未开始';
                \Yii::getLogger()->log('活动还未开始  $activity_id===:'.$activity['activity_id'],Logger::LEVEL_ERROR);
                continue;
            }

            if(($activity['end_time'] < $time) || ($activity['activity_status'] == 0))
            {
                $error = '活动已经结束了';
                \Yii::getLogger()->log('活动已经结束了  $activity_id===:'.$activity['activity_id'],Logger::LEVEL_ERROR);
                continue;
            }
            \Yii::getLogger()->log('data_gift:: living_master_id :'.$living_master_id.' activity_id:'.$activity['activity_id'].' ::get_scor::'.$get_score_gifts[$gift_id]['score'],Logger::LEVEL_ERROR);
            \Yii::getLogger()->flush(true);
            $transActions[] = new LivingMasterScoreSaveByTrans($living_master_id,$send_user_id,$activity['activity_id'],$get_score_gifts[$gift_id]['score']);
            $params = [
                'gift_id' =>  $gift_id,
                'integral' => $get_score_gifts[$gift_id]['score'],
                'send_user_id' => $send_user_id,
                'send_gift_time' => date('Y-m-d H:i:s'),
                'activity_id' => $activity['activity_id'],
                'living_master_id' => $living_master_id,
                'score_id' => '',         //排行榜表id
            ];
            $transActions[] = new LivingMasterScoreLogSaveByTrans($params);
        }

        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
//        $time2 = microtime(true);
        return true;
    }

    /**
     * 更新用户分享统计信息
     * @param $sentData
     * @param $error
     * @return bool
     */
    public static function UpdateShareLivingInfo($sentData ,&$error)
    {
        $transActions = [];
        $data_params = [
            'living_master_id'=>$sentData->living_master_id,
            'client_type'=>$sentData->client_type,
            'time'=>date('Y-m-d'),
        ];
        $share_params = [
            'share_type'=>$sentData->share_type,
            'time'=>date('Y-m-d'),
        ];
        $transActions[] = new LivingMasterShareByTrans($data_params);  //处理主播分享信息统计
        $transActions[] = new LivingShareStatisticByTrans($share_params); //处理各渠道分享信息统计

        if(!RewardUtil::RewardSaveByTransaction($transActions, $out, $error))
        {
            return false;
        }

        return true;
    }

    /**
     * 获取所有正在直播的群ID
     * @return array
     */
    public static function GetAllLivingMasterGroup()
    {
        $query = (new Query())
            ->select(['li.living_master_id user_id','cr.other_id group_id'])
            ->from('mb_living li')
            ->innerJoin('mb_chat_room cr','cr.living_id=li.living_id')
            ->where('li.status=2 and living_type <> 5 ')
            ->all();
        return $query;
    }
    /**
     * 获取所有正在直播的群ID 排除假直播
     */
    public static function GetAllLivingMasterGroupTest()
    {
        $query = (new Query())
            ->select(['li.living_master_id user_id','living_id'])
            ->from('mb_living li')
            ->where('li.status=2 and living_type <> 5')
            ->all();
        return $query;
    }

    /**
     * 添加密码直播的固定限制人数
     */
    public static function getLivingLimitNum(){
        $SystemParams = SystemParams::findOne(['code'=>'living_limit_num']);
        return $SystemParams;
    }

    /**
     * 修改特殊直播固定人数
     * @param $person_count
     * @param $living_id
     * @throws \yii\db\Exception
     */
    public static function UpdateLivingLimitNum($person_count,$living_id){
        $sql = 'update mb_living set limit_num=:ln WHERE Living_id=:li';

        \Yii::$app->db->createCommand($sql,[
            ':ln' => $person_count,
            ':li' => $living_id
        ])->execute();
    }

    public static function QuitRoomUpdateLivingLimitNum($living_id){
        $sql = 'update mb_living set limit_num=\'\' WHERE Living_id=:li';

        \Yii::$app->db->createCommand($sql,[
            ':li' => $living_id
        ])->execute();
    }


    /**
     * 更新已经使用过的room_no
     * @param $error
     * @return bool
     */
    public static function SetRoomNoIsUse($room_no,&$error)
    {
        $phpLock = new PhpLock('mb_room_no_list_lock');
        $phpLock->lock();
        $sql='update mb_room_no_list set is_use = 1 where room_no=:rno and is_use=0;';
        $data = \Yii::$app->db->createCommand($sql,[':rno' => $room_no])->execute();
        if($data === false)
        {
            $phpLock->unlock();
            $error = '房间号更新失败';
            \Yii::error($error.'  sql==:'.\Yii::$app->db->createCommand($sql,[':rno' => $room_no])->rawSql);
            return false;
        }
        $phpLock->unlock();
        return true;
    }

    /**
     * 门票直播观众信息表写数据
     * @param $living_id
     * @param $living_master_id
     * @param $living_before_id
     * @param $user_id
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SetLivingPasswrodTicketView($room_no,$user_id,&$error)
    {
        $living_password_info = LivingPasswrodTicket::findOne(['room_no' => $room_no]);
        if(!isset($living_password_info) || empty($living_password_info))
        {
            $error = '门票直播信息不存在';
            return false;
        }
        $insert_sql = 'insert ignore into mb_living_passwrod_ticket_views(tikcet_id,user_id) VALUES (:tid,:uid)';
        $insert_res = \Yii::$app->db->createCommand($insert_sql,[
            ':tid' => $living_password_info->tikcet_id,
            ':uid' => $user_id
        ])->execute();

        $update_sql = 'update mb_living_passwrod_ticket_views set remark1=:rem1 WHERE tikcet_id=:tid and user_id=:uid';
        $update_res = \Yii::$app->db->createCommand($update_sql,[
            ':rem1' => time(),
            ':tid' => $living_password_info->tikcet_id,
            ':uid' => $user_id
        ])->execute();
        if($update_res <= 0)
        {
            $error = '门票观众信息写入失败';
            \Yii::getLogger()->log($error.'  sql ==:'.\Yii::$app->db->createCommand($update_sql,[
                    ':rem1' => time(),
                    ':tid' => $living_password_info->tikcet_id,
                    ':uid' => $user_id
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    /**
     * 续播
     * @param $living_id
     * @param $room_no
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SetLivingContinue($living_id,&$error)
    {
        $sql = 'update mb_living set create_time=:time,status=:st where living_id=:lid';
        $living_res = \Yii::$app->db->createCommand($sql,[
            ':time' => date('Y-m-d H:i:s'),
            ':st' => 2,
            ':lid' => $living_id,
        ])->execute();
        if($living_res <= 0)
        {
            $error = '续播信信息修改失败';
            \Yii::getLogger()->log($error.'  sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':time' => date('Y-m-d H:i:s'),
                    ':st' => 2,
                    ':lid' => $living_id,
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 通过直播类型获取对应配置
     * @param $living_type
     * @return array|mixed
     */
    public static function GetLivingConf($living_type)
    {
        if($living_type == 3)
        {
            $guess_living_conf = SystemParamsUtil::GetSystemParam('guess_living_money',true,'value1'); //密码直播鲜花配置
            $guess_living_conf = explode(',',$guess_living_conf);
            sort($guess_living_conf);
            return $guess_living_conf;
        }
        elseif($living_type == 4)
        {
            $ticket_living_conf = SystemParamsUtil::GetSystemParam('guess_ticket_money',true,'value1'); //门票直播配置
            $ticket_living_conf = explode(',',$ticket_living_conf);
            sort($ticket_living_conf);
            return $ticket_living_conf;
        }
        return [];

    }

    /**
     * 获取直播间房间编号 和门票
     * @param $living_id
     * @param $user_id
     * @return array|bool
     */
    public static function GetLivingTicketGuess($living_id,$user_id)
    {
        $query = (new Query())
            ->select(['l.living_type','l.living_id','l.living_master_id as to_user_id','l.status','l.room_no','lpt.tickets','ptv.user_id as ticket_views','lpv.user_id as private_views','IFNULL(l.limit_num,\'\') as limit_num','blp.person_count'])
            ->from('mb_living l')
            ->innerJoin('mb_living_personnum blp','blp.living_id = l.living_id')
            ->leftJoin('mb_living_private lp','l.living_id = lp.living_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id = lp.private_id and lpv.user_id = :ud',[':ud'=>$user_id])
            ->leftJoin('mb_living_passwrod_ticket lpt','l.living_id = lpt.living_id and l.living_before_id = lpt.living_before_id')
            ->leftJoin('mb_living_passwrod_ticket_views ptv','ptv.tikcet_id = lpt.tikcet_id and ptv.user_id = :ud',[':ud'=>$user_id])
            ->where('l.living_id = :ld',[':ld'=>$living_id])
            ->one();

        return $query;
    }

    /**
     * 判断主播重新开直播，用户未刷新页面时，不用输入密码就可以进入
     * @param $living_type
     * @param $room_no
     * @param $error
     * @return bool
     */
    public static function CheckMoreEnterRoom($living_type,$room_no,&$error)
    {
        if($living_type == 3)
        {
            if(empty($room_no))
            {
                $error = '房间号不能为空';
                return false;
            }
            $info = LivingPrivate::findOne(['room_no' => $room_no]);
            if(!$info)
            {
                $error = '密码错误，请刷新重试';
                return false;
            }
        }
        elseif($living_type == 4)
        {
            if(empty($room_no))
            {
                $error = '房间号不能为空';
                return false;
            }
            $info = LivingPasswrodTicket::findOne(['room_no' => $room_no]);
            if(!$info)
            {
                $error = '密码错误，请刷新重试';
                return false;
            }
        }
        return true;
    }

    /**
     * 获取一条未使用的房间号
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function GetRoomNoOne(&$error)
    {
        $phpLock = new PhpLock('mb_room_no_list_get_one_room_no');
        $phpLock->lock();
        $trans =\Yii::$app->db->beginTransaction();
        try
        {
            $sql='select room_no from mb_room_no_list where status = 1 and is_use = 0 limit 1 ';
            $data = \Yii::$app->db->createCommand($sql)->queryOne();
            if($data === false)
            {
                $phpLock->unlock();
                $error = '未获取到数据请从新获取';
                $trans->rollBack();
                return false;
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $phpLock->unlock();
            $error = $e->getMessage();
            $trans->rollBack();
            $error = 'sql执行错误';
            return false;
        }
        $phpLock->unlock();
        return $data['room_no'];
    }

    /**
     * 获取直播开播提示语
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function GetCreateLivingNotice()
    {
        $query = SystemMessage::find()
            ->select(['system_message'])
            ->where('status =1')
            ->orderBy('message_id ASC')
            ->one();
        return $query;
    }

    /**
     * 获取正在直播的数据
     */
    public static function GetLiveLiving()
    {
        $query = (new Query())
            ->select(['living_id','mc.client_no','mc.nick_name','pull_http_url','pull_rtmp_url','pull_hls_url'])
            ->from('mb_living ml')
            ->innerJoin('mb_client mc','ml.living_master_id = mc.client_id')
            ->where('ml.status = 2 and ml.living_type != 5')
            ->all();
        return $query;
    }

    /**
     * 后台封播，封播用户
     * @param $living_id
     */
    public static function SealLiving($living_id,$type)
    {
        $qurey = new Query();
        $qurey->from('mb_living li')->select(['li.living_before_id','li.living_id','living_master_id','cr.other_id','finish_time'])
            ->innerJoin('mb_chat_room cr','li.living_id = cr.living_id')
            ->where(['li.living_id'=>$living_id]);
        $living = $qurey->one();

        if($type == 2)
        {
            if( !StopLivingUtil::StopLiving($living_id,$living['living_master_id'],3,$error) )
            {
                ClearCacheHelper::ClearHotLivingDataCache();
                $rstData['errno']     = 1;
                $rstData['errmsg']    = $error;
                \Yii::getLogger()->log('操作封播时发生了错误：详情请看日记:'.$error,Logger::LEVEL_ERROR);
                return false;
            }
        }
        else
        {
            if( !StopLivingUtil::StopLiving($living_id,$living['living_master_id'],1,$error) )
            {
                ClearCacheHelper::ClearHotLivingDataCache();
                $rstData['errno']     = 1;
                $rstData['errmsg']    = $error;
                \Yii::getLogger()->log('操作封播时发生了错误：详情请看日记:'.$error,Logger::LEVEL_ERROR);
                return false;
            }
        }

        //禁用用户，直接结束直播
        $finishInfo = null;

        //发送im消息
        if( !LivingUtil::SetBanClientFinishLivingToStopLiving( $living_id,$finishInfo,$living['living_master_id'],$living['other_id'],$outInfo,$error) )
        {
            ClearCacheHelper::ClearHotLivingDataCache();
            $rstData['errno']     = 1;
            $rstData['errmsg']    = $error;
            \Yii::getLogger()->log('操作封播时发生了错误：详情请看日记:'.$error,Logger::LEVEL_ERROR);
            return false;
        }


        if(LivingUtil::SetBanClientFinishLiving($living_id,$finishInfo,$living['living_master_id'],$living['other_id'],$outInfo,$error))
        {
            ClearCacheHelper::ClearHotLivingDataCache();
            $finish_time = $living['finish_time'];
            $now_time = date('Y-m-d H:i:s');
            \Yii::getLogger()->log('关闭直播：'.' living_id:'.$living['living_id'].$now_time.' finish_time:'.$finish_time,Logger::LEVEL_ERROR);

            //关闭直播日志表写入记录
            $close_living_model = new CloseLivingLog();
            $close_living_model->living_id = $living['living_id'];
            $close_living_model->living_before_id = $living['living_before_id'];
            $close_living_model->close_time = date('Y-m-d H:i:s');
            $close_living_model->backend_user_id = \Yii::$app->user->identity->id;
            $close_living_model->backend_user_name = \Yii::$app->user->identity->username;
            if(!CloseLivingLogUtil::CloseLivingLogSave($close_living_model,$error))
            {
                echo(\yii\helpers\Json::encode(array('code'=>1,'msg' => $error)));
                exit;
            }
            echo(\yii\helpers\Json::encode(array('code'=>0)));

        }else{
            \Yii::getLogger()->log('关闭直播是发生了错误',Logger::LEVEL_ERROR);
            echo(\yii\helpers\Json::encode(array('code'=>1)));
        }


    }

    /**
     * 私密直播写入缓存
     */
    public static function privateLivingCache($livingId,$living_before_id,$pwd,$userId,&$error)
    {
        $cache_key = 'private_living_info_'.$livingId;
        $cache_data = [
            'living_before_id' => $living_before_id,
            'living_id' => $livingId,
            'password' => $pwd,
            'user_id' => $userId
        ];
        $cache_data = json_encode($cache_data);
        $cache = \Yii::$app->cache->set($cache_key,$cache_data,3600*24*2);
        if(!$cache)
        {
            $error = '私密直播缓存写入失败';
            \Yii::error($error);
            return false;
        }

        return true;
    }
} 