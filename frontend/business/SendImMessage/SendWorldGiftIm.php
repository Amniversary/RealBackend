<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 11:27
 */

namespace frontend\business\SendImMessage;


use common\components\tenxunlivingsdk\TimRestApi;
use yii\log\Logger;

/**
 * 世界礼物发送 im
 * Class SendWorldGiftIm
 * @package frontend\business\SendImMessage
 */
class SendWorldGiftIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }
        $sendInfo = [
            'type' => 11,
            'gift_name' => $jobData->gift_name,
            'send_user_id' => $jobData->send_user_id,
//            'send_pic' => $jobData->send_pic,
            'send_pic' => '',
            'send_nick_name' => $jobData->send_nick_name,
            'accept_nick_name' => $jobData->accept_nick_name,
        ];
        $text = json_encode($sendInfo);
        if(!TimRestApi::group_send_group_msg_custom((string)$jobData->user_id,$jobData->other_id,$text,$error))
        {
            \Yii::getLogger()->log('发送世界消息失败  user_id==:'.$jobData->user_id,'   other_id==:'.$jobData->other_id,Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }
} 