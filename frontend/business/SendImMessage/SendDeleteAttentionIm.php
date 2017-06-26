<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 10:28
 */

namespace frontend\business\SendImMessage;


use common\components\tenxunlivingsdk\TimRestApi;

class SendDeleteAttentionIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params =[])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $user_id = $jobData->user_id;
        $attention_id = $jobData->attention_id;

        if(!TimRestApi::sns_friend_delete($user_id,$attention_id,$error))
        {
            return false;
        }

        return true;
    }
} 