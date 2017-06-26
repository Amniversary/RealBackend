<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/2
 * Time: 11:05
 */

namespace backend\business;


use common\models\SetUserCheckNo;
use yii\log\Logger;

class SetUserCheckNoUtil
{
    /**
     * 根据用户id，获取审核设置
     * @param $user_id
     */
    public static function GetUserCheckNoByUserId($user_id)
    {
        return SetUserCheckNo::findOne(['user_id'=>$user_id]);
    }

    /**
     * 保存记录
     * @param $setUserCheckNo
     * @param $error
     */
    public static function Save($setUserCheckNo,&$error)
    {
        if(!($setUserCheckNo instanceof SetUserCheckNo))
        {
            $error = '不是设置审核号记录';
            return false;
        }
        if(!$setUserCheckNo->save())
        {
            $error = '保存审核号信息失败';
            \Yii::getLogger()->log($error.' :'. var_export($setUserCheckNo->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

} 