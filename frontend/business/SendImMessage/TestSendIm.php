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
 * 测试IM消息
 * Class TestSendIm
 * @package frontend\business\SendImMessage
 */
class TestSendIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $sendInfo = [
            'type' => $jobData->type,
            'isAdministrator' => $jobData->isAdministrator,
            'msg' => $jobData->msg,
            'userinfo' => $jobData->userinfo,
        ];

        $text = json_encode($sendInfo);

        if(!TimRestApi::group_send_group_msg_custom((string)$jobData->user_id,$jobData->other_id,$text,$error))
        {
            return false;
        }

        return true;
    }
} 