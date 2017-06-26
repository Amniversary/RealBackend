<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/30
 * Time: 16:29
 */

namespace frontend\business\SendImMessage;


use frontend\business\RongCloud\SystemMessageUtil;

class SendLevelIm implements ImExcute
{
    function excute_im($jobData,&$error,$params=[])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $RongY = new SystemMessageUtil();
        $RongY->sendSystemMessage($jobData->userId,'',['level_no'=>$jobData->levelNo],111);

        return true;
    }
} 