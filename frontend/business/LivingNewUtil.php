<?php

namespace frontend\business;

use frontend\business\LivingUtil;
use frontend\business\RongCloud\ChatroomMessageUtil;
use yii\log\Logger;

class LivingNewUtil
{
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

        $sendInfo = [
            'attend_user_count'=>$finishInfo['attend_user_count'],
            'tickets_num'=>sprintf('%d',$finishInfo['tickets_num']),
            'living_time'=>$finishInfo['living_time'],
            'operator_type' => '2',
        ];

        $chatroomHelper = new ChatroomMessageUtil();

        $user = ClientUtil::getClientActive($living_master_id);
        $rUser = [
            'id' => $user['user_id'],
            'name' => $user['nick_name'],
            'icon' => $user['pic']
        ];
        $result = $chatroomHelper->sendBanClientMessage($living_id, $sendInfo, $rUser);
        if ($result !== true) {
            $error = $result;
            return false;
        }

        return true;
    }
} 