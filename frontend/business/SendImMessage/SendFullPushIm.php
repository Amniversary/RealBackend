<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/29
 * Time: 9:53
 */

namespace frontend\business\SendImMessage;


use common\components\tenxunlivingsdk\TimRestApi;

class SendFullPushIm implements ImExcute
{
    function excute_im($jobData,&$error,$params=[])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }


        if(!TimRestApi::openim_batch_send_msg($jobData->msg, $error))
        {
            $rst['msg']= '发送推送消息失败:'.$error;
            echo json_encode($rst);
            exit;
        }

        return true;
    }
} 