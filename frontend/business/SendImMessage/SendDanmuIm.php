<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7
 * Time: 10:38
 */

namespace frontend\business\SendImMessage;



use yii\helpers\Console;

class SendDanmuIm implements ImExcute
{
    function excute_im($jobData,&$error,$params=[])
    {
        if(!$jobData instanceof \stdClass)
        {
            $error = '数据异常，不是json对象';
            return false;
        }
        fwrite(STDOUT, Console::ansiFormat("DanmuIm:".var_export($jobData,true)."\n", [Console::FG_GREEN]));
        $userId = $jobData->user->id;
        $roomId = $jobData->living_id;
        unset($jobData->key_word);
        unset($jobData->living_id);
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