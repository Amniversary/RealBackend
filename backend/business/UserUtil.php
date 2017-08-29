<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/6
 * Time: 18:50
 */

namespace backend\business;


use Codeception\Module\Cli;
use common\models\Client;
use common\models\Family;
use common\models\User;
use yii\log\Logger;

class UserUtil
{
    /**
     * 保存用户信息
     * @param $user
     * @param $error
     * @return bool
     */
    public static function SaveUser($user, &$error)
    {
        if(!($user instanceof User)) {
            $error = '不是后台用户对象';
            return false;
        }
        if(!$user->save()) {
            $error = '保存用户信息失败';
            \Yii::error($error . '_' . var_export($user->getErrors(), true));
            return false;
        }
        return true;
    }

    /**
     * 根据id获取用户
     * @param $userId
     * @return User|null
     */
    public static function GetUserByUserId($userId)
    {
        return User::findOne(['backend_user_id' => $userId]);
    }

    /**
     * 重置密码
     * @param $params
     * @param $user_id
     * @param $error
     */
    public static function ResetPwd($user, &$error)
    {
        if (!($user instanceof User)) {
            $error = '不是后台用户对象';
            return false;
        }
        $user->ResetPwd();
        \Yii::getLogger()->log($user->password, Logger::LEVEL_ERROR);
        if (!$user->save()) {
            $error = '保存用户密码异常';
            \Yii::getLogger()->log($error . ' :' . var_export($user->getErrors(), true), Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * @param $user_id
     * @return null|Client
     */
    public static function  GetClientInfo($user_id){
       return Client::findOne(['client_id'=>$user_id]);
    }
}