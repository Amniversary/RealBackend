<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/20
 * Time: 16:29
 */

namespace frontend\business\SendImMessage;

use frontend\business\RongCloud\SystemMessageUtil;

class SendDynamicRyIm implements ImExcute
{
    function excute_im($jobData,&$error,$params=[])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $data =  [
            'user_client_id' => $jobData->user_id,
            'user_nick_name' => $jobData->nick_name,
            'user_icon_pic'  => $jobData->pic,
            'dynamic_id'     => $jobData->dynamic_id,
            'dynamic_pic'    => $jobData->dynamic_pic,
            'create_time'    => $jobData->create_time,
        ];

        $content = '';
        switch ($jobData->type) {
            case 1:
                $content = "查看了你的照片并打赏{$jobData->reward_money}朵鲜花";
                $data['reward_money'] = $jobData->reward_money;
                break;
            case 2:
                $content = "评论了你的照片：" . $jobData->content;
                break;
            case 3:
                $content = "对你的照片点了赞";
                break;
        }

        $messageHelper = new SystemMessageUtil();
        $messageHelper->sendGeneralMessage($jobData->to_user_id, $content, $data);

        return true;
    }
} 