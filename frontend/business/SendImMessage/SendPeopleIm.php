<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/25
 * Time: 15:23
 */

namespace frontend\business\SendImMessage;


use common\components\tenxunlivingsdk\TimRestApi;
use yii\log\Logger;

class SendPeopleIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }
        if (isset($jobData->extra) && 'test-ry' == $jobData->extra) {
            $messageHelper = new \frontend\business\RongCloud\ChatroomMessageUtil();
            $msgExtra = json_decode($jobData->sv, true);

            if (isset($jobData->tag) && $jobData->tag == 306) {
                // 发送欢迎
                $rst = $messageHelper->sendChatroomOtherMsg($jobData->living_id, $msgExtra, $jobData->tag);
            } else {
                // 发送人数
                $rst = $messageHelper->sendChatroomOtherMsg($jobData->living_id, $msgExtra);
            }

            if ($rst !== true) {
                \Yii::error($rst);
                \Yii::getLogger()->flush(true);
                return false;
            }
        } else {
            if(!TimRestApi::group_send_group_msg_custom($jobData->user_id,$jobData->chat_room,$jobData->sv,$error))
            {
                \Yii::getLogger()->log('发送人数im消息失败：'.'fail:'.$error.' date_time:'.date('Y-m-d H:i:s'),Logger::LEVEL_ERROR);
                \Yii::getLogger()->flush(true);
                return false;
            }
        }

        return true;
    }
} 