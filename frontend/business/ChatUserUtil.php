<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/3/9
 * Time: 11:15
 */

namespace frontend\business;


use common\models\AccountInfo;

class ChatUserUtil
{
    /**
     * 注册所有用户到环信
     * @param $error
     * @return bool
     */
    public static function RegisterAllUsersToHuanXin(&$error)
    {
        $usersList = AccountInfo::find()->select(['account_id'])->all();
        $user_id_list = [];
        foreach($usersList as $user)
        {
            $user_id_list[] = $user->account_id;
        }
        if(!ChatUtilHuanXin::BatchRegisterUser($user_id_list,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 获取个人聊天背景图片列表
     */
    public static function GetChatPicList()
    {
        return [

        ];
    }
} 