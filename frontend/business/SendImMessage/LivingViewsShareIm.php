<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 11:27
 */

namespace frontend\business\SendImMessage;


use common\components\tenxunlivingsdk\TimRestApi;
use frontend\business\RongCloud\ChatroomMessageUtil;
use yii\log\Logger;

/**
 * 分享IM消息
 * Class LivingViewsShareIm
 * @package frontend\business\SendImMessage
 */
class LivingViewsShareIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $sendInfo = [
            'type' => 13,
            'user_id' => $jobData->user_id,
            'nick_name' => $jobData->nick_name,
            'level_id' => $jobData->level_no,
            'beans' => $jobData->beans,
        ];

        if (isset($jobData->extra) && $jobData->extra == 'test_ry') {
            $messageHelper = new ChatroomMessageUtil();
            $rst = $messageHelper->sendChatroomOtherMsg($jobData->living_id, $sendInfo, $messageHelper::MSG_SEND_SHOW_TAG);

        } else {
            $text = json_encode($sendInfo);

            if(!TimRestApi::group_send_group_msg_custom((string)$jobData->user_id,$jobData->other_id,$text,$error))
            {
                return false;
            }
        }

        return true;
    }
} 