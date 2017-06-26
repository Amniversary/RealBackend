<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/20
 * Time: 16:29
 */

namespace frontend\business\SendImMessage;


use common\components\tenxunlivingsdk\TimRestApi;

class SendDynamicIm implements ImExcute
{
    function excute_im($jobData,&$error,$params=[])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $data = [
            'user_id'=>$jobData->user_id,
            'nick_name'=>$jobData->nick_name,
            'pic'=>$jobData->pic,
            'content'=>$jobData->content,
            'dynamic_id'=>$jobData->dynamic_id,
            'dynamic_pic'=>$jobData->dynamic_pic,
            'create_time'=>$jobData->create_time,
            'type'=>$jobData->type,
        ];
        if($jobData->type == 1)
        {
            $data['reward_money'] = $jobData->reward_money;
        }
        $text = json_encode($data);

        if(!TimRestApi::openim_send_Text_msg2($jobData->to_user_id, $text, $error))
        {
            return false;
        }

        return true;
    }
} 