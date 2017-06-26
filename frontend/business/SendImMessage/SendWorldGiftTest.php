<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15
 * Time: 19:03
 */

namespace frontend\business\SendImMessage;


use yii\helpers\Console;

class SendWorldGiftTest implements ImExcute
{
    public function excute_im($jobData,&$error,$params = [])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        fwrite(STDOUT, Console::ansiFormat("WorldIm:".var_export($jobData,true)."\n", [Console::FG_GREEN]));
        $userId = $jobData->user_id;
        $roomId = $jobData->other_id;
        unset($jobData->user_id);
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