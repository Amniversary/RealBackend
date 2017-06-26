<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/14
 * Time: 21:16
 */

namespace frontend\business\SendImMessage;


use yii\helpers\Console;

class SendGiftImTest implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }
        fwrite(STDOUT, Console::ansiFormat("GiftIm:".var_export($jobData,true)."\n", [Console::FG_GREEN]));
        $userId = $jobData->user->id;
        $roomId = $jobData->other_id;
        unset($jobData->key_word);
        unset($jobData->other_id);
        $json = json_encode($jobData);
        $rc = \Yii::$app->im->Message();
        if(!$rc->publishChatroom($userId,$roomId,'MB:gifts',$json))
        {
            $error = $rc->getErrorMessage();
            return false;
        }

        return true;
    }
} 