<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13
 * Time: 18:15
 */

namespace frontend\business\SendImMessage;
use yii\helpers\Console;


/**
 * 幸运礼物IM消息
 * Class SendLuckyGiftIm
 * @package frontend\business\SendImMessage
 */
class SendLuckyGift implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }
        fwrite(STDOUT, Console::ansiFormat("LuckyIm:".var_export($jobData,true)."\n", [Console::FG_GREEN]));
        $roomId = $jobData->other_id;
        unset($jobData->key_word);
        unset($jobData->other_id);
        $json = json_encode($jobData);
        $rc = \Yii::$app->im->Message();
        if(!$rc->publishChatroom('-1',$roomId,'MB:gifts',$json))
        {
            $error = $rc->getErrorMessage();
            return false;
        }

        return true;
    }

} 