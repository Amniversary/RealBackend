<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/6
 * Time: 18:50
 */

namespace backend\business;


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
     * 根据家族长id 获取家族长信息
     * @param $family_id
     * @return null|static
     */
    public static function GetFamilyById($family_id)
    {
        return Family::findOne(['family_id' => $family_id]);
    }

    /**
     * 保存家族长账号信息
     * @param $family
     * @param $error
     * @return bool
     */
    public static function SaveFamily($family , &$error)
    {
        if(! $family instanceof Family)
        {
            $error = '不是家族用户对象';
            return false;
        }

        if(!$family->save())
        {
            $error = '保存家族长账号信息异常';
            \Yii::getLogger()->log($error.' : '.var_export($family->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }

    /**
     * 修改家族长账号密码
     * @param $Family
     * @param $error
     * @return bool
     */
    public static function FamilyResetPwd($Family ,&$error)
    {
        if (!($Family instanceof Family)) {
            $error = '不是后台用户对象';
            return false;
        }
        $Family->ResetPwd();
        \Yii::getLogger()->log($Family->password, Logger::LEVEL_ERROR);
        if (!$Family->save()) {
            $error = '保存用户密码异常';
            \Yii::getLogger()->log($error . ' :' . var_export($Family->getErrors(), true), Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }
}