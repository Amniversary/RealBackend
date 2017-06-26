<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 11:27
 */

namespace frontend\business\SendImMessage;


use common\components\tenxunlivingsdk\TimRestApi;


class SendGiftIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $sendInfo = [
            'type' => 2,
            'tickets_num' => $jobData->tickets_num,
        ];

        $text = json_encode($sendInfo);

        if(!TimRestApi::group_send_group_msg_custom((string)$jobData->user_id,$jobData->other_id,$text,$error))
        {
            return false;
        }

        return true;
    }
} 