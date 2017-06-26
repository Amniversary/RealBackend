<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 10:49
 */

namespace frontend\business\SendImMessage;


use common\components\tenxunlivingsdk\TimRestApi;

class SendTencentIm implements ImExcute
{
    public function excute_im($jobData,&$error,$params=[])
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $userId = strval($jobData->user_id);
        $nickName = $jobData->nick_name;
        $Pic = $jobData->pic;

        if(!TimRestApi::account_import($userId,$nickName,$Pic,$error))
        {
            return false;
        }

        return true;
    }
} 