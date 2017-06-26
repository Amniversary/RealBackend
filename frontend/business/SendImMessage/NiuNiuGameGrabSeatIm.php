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
use yii\helpers\Console;
use yii\log\Logger;

/**
 * 牛牛游戏抢座IM消息
 * Class NiuNiuGameGrabSeatIm
 * @package frontend\business\SendImMessage
 */
class NiuNiuGameGrabSeatIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $sendInfo = [
            'type' => 14,
            'seat_user_info' => $jobData->im_user_datas
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