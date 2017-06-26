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
 * 直播间右上角排行榜
 * Class SendLivingAudienceIm
 * @package frontend\business\SendImMessage
 */
class SendLivingAudienceIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $sendInfo = [
            'type' => 27,
            'client_no' => $jobData->client_no,
            'owner' => $jobData->owner,
            'user_id' => $jobData->user_id,
            'pic' => $jobData->pic,
            'is_police' => $jobData->is_police,
            'is_attention' => $jobData->is_attention,
            'nick_name' => $jobData->nick_name,
            'sex' => $jobData->sex,
            'sign_name' => $jobData->sign_name,
            'send_ticket_count' => $jobData->send_ticket_count,
            'ticket_count_sum' => $jobData->ticket_count_sum,
            'first_reward' => $jobData->first_reward,
        ];
        $text = json_encode($sendInfo);

        if(!TimRestApi::group_send_group_msg_custom((string)$jobData->my_user_id,$jobData->other_id,$text,$error))
        {
            return false;
        }

        return true;
    }
} 