<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 11:27
 */

namespace frontend\business\SendImMessage;


use common\components\tenxunlivingsdk\TimRestApi;

/**
 * 幸运礼物IM消息
 * Class SendLuckyGiftIm
 * @package frontend\business\SendImMessage
 */
class SendLuckyGiftIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $sendInfo = [
            'type' => 12,
            'user_id' => $jobData->user_id,
            'nick_name' => $jobData->nick_name,
            'level_id' => $jobData->level_no,
            'multiple' => $jobData->multiple,
            'pic' => $jobData->pic,
            'total_beans' => $jobData->total_beans
        ];

        $text = json_encode($sendInfo);

        if(!TimRestApi::group_send_group_msg_custom((string)$jobData->user_id,$jobData->other_id,$text,$error))
        {
            return false;
        }

        return true;
    }
} 