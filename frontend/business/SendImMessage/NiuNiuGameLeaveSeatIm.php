<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 11:27
 */

namespace frontend\business\SendImMessage;


use common\components\tenxunlivingsdk\TimRestApi;
use frontend\business\SendImUtil;
use yii\log\Logger;

/**
 * 牛牛游戏用户离开座位IM消息
 * Class NiuNiuGameLeaveSeatIm
 * @package frontend\business\SendImMessage
 */
class NiuNiuGameLeaveSeatIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $sendInfo = [
            'type' => 17,
            'seat_num' => $jobData->seat_num,
            'nick_name' => $jobData->nick_name,
            'pic' => $jobData->pic,
            'seat_status' => $jobData->seat_status,
        ];

        $text = json_encode($sendInfo);

        if(!SendImUtil::SendImMsg($jobData->user_id,$jobData->other_id,$text,$error))
        {
            \Yii::getLogger()->log('发送IM消息失败  error===:'.$error,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
} 