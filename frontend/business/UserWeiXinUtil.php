<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/25
 * Time: 17:09
 */

namespace frontend\business;


use common\components\PhpLock;
use common\models\UserWeixin;
use common\models\WechatLivingOff;
use common\models\WxLiveManage;
use yii\log\Logger;

class UserWeiXinUtil
{
    /**
     * 完善用户信息
     * @param $phone_no
     * @param $open_id
     * @param $error
     * @return bool
     */
    public static function UpdateUserInfo($phone_no,$open_id,&$error)
    {
        $userInfo = PersonalUserUtil::GetAccountInfoByPhoneNo($phone_no);
        if(!isset($userInfo))
        {
            if(!PersonalUserUtil::RegisterUser($phone_no,sha1($open_id),$error))
            {
                if($error !== '用户已经注册')
                {
                    return false;
                }
            }
        }
        $userInfo = PersonalUserUtil::GetAccountInfoByPhoneNo($phone_no);
        $user_id = $userInfo->account_id;
        $wxUser = self::GetUserWeiXinInfo($open_id);
        if(!isset($wxUser))
        {
            $error = '微信用户信息丢失';
            return false;
        }
        if(!empty($wxUser->user_id) && $wxUser->user_id != $user_id)
        {
            $error = '登录异常，用户信息错误';
            return false;
        }

        $wxUser->user_id = $user_id;
        if(!$wxUser->save())
        {
            $error = '绑定微信账户信息异常';
            \Yii::getLogger()->log('微信用户信息保存失败：'.var_export($wxUser->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 根据微信id获取用户信息
     * @param $open_id
     */
    public static function GetUserWeiXinInfo($open_id)
    {
        return UserWeixin::findOne(['open_id'=>$open_id]);
    }

    /**
     * 检测微信端是否登录过，已经获取open_id并且已经关联手机算已经登录过，否则未登录，需要重新登录
     * @param $open_id
     * @param int $user_id
     * @param $error
     * @return bool
     */
    public static function CheckLogin($open_id,&$user_id,&$error)
    {
        $wxUserInfo = self::GetUserWeiXinInfo($open_id);
        if(!isset($wxUserInfo))
        {
            $lock = new PhpLock('wxuser_'.$open_id);
            $lock->lock();
            $wxUserInfo = self::GetUserWeiXinInfo($open_id);
            if(!isset($wxUserInfo))
            {
                $wxUserInfo = self::GetUserWeiXinNewModel($open_id);
                if(!self::SaveUserWeiXinInfo($wxUserInfo,$error))
                {
                    $lock->unlock();
                    return false;
                }
            }
            $lock->unlock();
        }
        if(!empty($wxUserInfo->user_id))
        {
            $user_id = $wxUserInfo->user_id;
        }
        else
        {
            return false;
        }
        return true;
    }

    public static function SaveUserWeiXinInfo($model, &$error)
    {
        if(!$model instanceof UserWeixin)
        {
            $error = '不是微信用户关系对象';
            return false;
        }
        if(!$model->save())
        {
            $error = '微信用户信息保存失败';
            \Yii::getLogger()->log(var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    public static function GetUserWeiXinNewModel($open_id)
    {
        $model = new UserWeixin();
        $model->open_id = $open_id;
        $model->is_note = 0;
        return $model;
    }

    /**
     * 根据用户蜜播id ,获取微信管理员信息
     * @param $client_no
     */
    public static function GetWeChatLiveManage($client_no)
    {
        return WxLiveManage::findOne(['client_no'=>$client_no]);
    }


    /**
     * 写入微信开关直播信息记录
     * @param $client //用户信息
     * @param $operate_type //操作类型
     * @param $error
     * @return bool
     */
    public static function CreateWeChatLivingOff($client,$operate_type,&$error)
    {
        $model = new WechatLivingOff();
        $data = [
            'client_no'=>$client->client_no,
            'user_name'=>$client->name,
            'operate_type'=>$operate_type,
            'create_time'=>date('Y-m-d H:i:s'),
        ];

        $model->attributes = $data;
        if(!$model->save())
        {
            $error = '写入微信开关直播记录失败';
            \Yii::getLogger()->log($error.' :'.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
} 