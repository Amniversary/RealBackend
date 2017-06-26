<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/9
 * Time: 19:29
 */

namespace frontend\business;
use common\components\SystemParamsUtil;
use yii\log\Logger;

/**
 * 心跳业务类
 * Class HeartBeatUtil
 * @package frontend\business
 */
class HeartBeatUtil
{
    /**
     * 处理心跳
     * @param $passData
     * @param $error
     */
    public static function DealHeartBeat($passData,&$error)
    {
        if(empty($passData) || !is_array($passData))
        {
            $error = '参数错误';
            return false;
        }
        if(empty($passData['living_id']) || empty($passData['user_id']))
        {
            $error = '心跳缺少参数';
            return false;
        }
        $living_id = $passData['living_id'];
        $user_id = $passData['user_id'];
        $time = $passData['heart_time'];
//        $livingInfo = LivingUtil::GetLivingById($living_id);

        $roomInfo = ChatPersonGroupUtil::GetChatRoomMember($living_id,$user_id);
        if(!$roomInfo)
        {
            \Yii::getLogger()->log('直播不存在,living_id'.$living_id.' $user_id:'.$user_id, Logger::LEVEL_ERROR);
            $error = '直播不存在';
            return false;
        }
//        \Yii::getLogger()->log('心跳心跳,$roomInfo'.var_export($roomInfo,true).'living_id=='.$living_id.'  user_id=:'.$user_id, Logger::LEVEL_ERROR);
        if($roomInfo['status'] != 2)
        {
            \Yii::getLogger()->log('不是直播中状态，心跳无效,living_id'.$living_id.' status:'.$roomInfo['status'], Logger::LEVEL_ERROR);
            return true;
        }
//        $chatRoom = ChatGroupUtil::GetChatGroupByLivingId($living_id);
//        if(!isset($chatRoom))
//        {
//            $error = '直播间不存在';
//            return false;
//        }
//        $roomMember = ChatPersonGroupUtil::GetGroupUser($chatRoom->room_id,$user_id);
//        if(!isset($roomMember))
//        {
//            $error = '不是直播间成员';
//            return false;
//        }
        $owner = $roomInfo['owner'];
        $enableDisHeartTime = SystemParamsUtil::GetSystemParam('unable_heart_dis_time',true,'value1');
        $enableDisHeartTime = intval($enableDisHeartTime);
        if(empty($enableDisHeartTime))
        {
            $error = '异常心跳参数丢失';
            return false;
        }
        //$time = date('Y-m-d H:i:s');//Y-m-d H:i:s
        if($owner == 1)
        {
            $sql = '
select @heartcount:=count(1) from mb_living where finish_time < date_add(:ti1,interval -12 second) and
finish_time >  date_add(:ti,interval -:sdis second) and
living_id=:lid1;
update mb_living set finish_time=:ti2,heart_count = heart_count + @heartcount where status = 2 and living_id=:lid';
            $rst = \Yii::$app->db->createCommand($sql,[
                ':ti'=>$time,
                ':ti1'=>$time,
                ':ti2'=>$time,
                ':sdis'=>$enableDisHeartTime,
                ':lid1'=>$living_id,
                ':lid'=>$living_id
            ])->execute();
            if($rst <= 0 )
            {
                $error = '更新心跳时间异常';
                return false;
            }
        }
        else
        {
            //不是主播不再用心跳，结束直播后做经验处理
/*            $sql = '
select @heartcount:=count(1) from mb_chat_room_member where modify_time < UNIX_TIMESTAMP(date_add(:ti1,interval -12 second)) and
modify_time >  UNIX_TIMESTAMP(date_add(:ti,interval -:sdis second)) and
record_id=:rid1;
update mb_chat_room_member set modify_time=:ti3,
heart_count=heart_count + @heartcount
where status = 1 and record_id=:rid';
            $rst = \Yii::$app->db->createCommand($sql,[
                ':ti'=>$time,
                ':ti1'=>$time,
                ':ti3'=>strtotime($time),
                ':sdis'=>$enableDisHeartTime,
                ':rid1'=>$roomMember->record_id,
                ':rid'=>$roomMember->record_id
            ])->execute();*/
        }
        //\Yii::getLogger()->log('更新心跳：user_id:'.strval($roomMember->user_id).' time:'.strval(strtotime($time)),Logger::LEVEL_ERROR);
        //\Yii::getLogger()->flush(true);

            return true;
    }
} 