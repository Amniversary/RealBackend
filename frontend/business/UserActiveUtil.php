<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/16
 * Time: 9:46
 */

namespace frontend\business;
use common\components\PhpLock;
use common\models\ClientActive;
use common\models\UserActive;
use yii\base\Exception;
use yii\log\Logger;

/**
 * Class 用户活动情况表辅助类
 * @package frontend\business
 */
class UserActiveUtil
{
    /**
     * 根据用户id获取，用户活跃信息
     * @param $user_id
     * @return null|static
     */
    public static function GetUserActiveByUserId($user_id)
    {
        return ClientActive::findOne([
            'user_id'=>$user_id
        ]);
    }

    /**
     * 获取活跃度表新模型，已经初始化值
     * @param $user_id
     * @return UserActive
     */
    public static function GetUserActiveNewModel($user_id)
    {
        $userActive = new UserActive();
        $userActive->user_id = $user_id;
        $userActive->reward_money_sum = 0;
        $userActive->reward_count = 0;
        $userActive->delay_times = 0;
        $userActive->wish_publish_count = 0;
        $userActive->wish_finish_money = 0;
        $userActive->wish_finish_count = 0;
        $userActive->wish_unfinish_count = 0;
        $userActive->be_reported_count = 0;
        $userActive->sign_circle_count = 0;
        $userActive->sign_count = 0;
        $userActive->sign_sum_count = 0;
        $userActive->balance_cash_money = 0;
        $userActive->balance_cash_count = 0;
        $userActive->fund_cash_count=0;
        $userActive->fund_cash_money=0;
        $userActive->fund_back_count=0;
        $userActive->fund_back_money=0;
        $userActive->check_refused_count=0;
        $userActive->check_refused_content='';
        return $userActive;
    }

    /**
     * 修改用户活跃度信息
     * @param $userActive
     * @param $error
     * @param $params
     */
    public static function ModifyUseractive($modify_type,$userActive,&$error,$params=[])
    {
        $error = '';
        if(!($userActive instanceof UserActive))
        {
            $error = '不是活跃度记录对象';
            return false;
        }
        $wishMofiyConfigFile = __DIR__.'/UserActiveModifyActions/UserActiveModifyConfig.php';
        if(!file_exists($wishMofiyConfigFile))
        {
            $error = '修改活跃度配置文件不存在';
            \Yii::getLogger()->log($error.' file:'.$wishMofiyConfigFile,Logger::LEVEL_ERROR);
            return false;
        }
        $wishModifyConfig = require($wishMofiyConfigFile);
        if(!isset($wishModifyConfig[$modify_type]))
        {
            $error = '修改活跃度类型不正确';
            \Yii::getLogger()->log($error.' modify_type:'.$modify_type,Logger::LEVEL_ERROR);
            return false;
        }
        $wishModifyClass = 'frontend\business\UserActiveModifyActions\\'.$wishModifyConfig[$modify_type];
        if(!class_exists($wishModifyClass))
        {
            $error = '修改活跃度类不存在';
            \Yii::getLogger()->log($error.' class:'.$wishModifyClass,Logger::LEVEL_ERROR);
            return false;
        }
        $wish_id = $userActive->user_id;
        $phpLock = new PhpLock('useractive_modify_'.strval($wish_id));
        $phpLock->lock();
        try
        {
            $modifyInstance = new $wishModifyClass;
            if(!$modifyInstance->UserActiveModify($userActive,$error,$params))
            {
                $phpLock->unlock();
                return false;
            }
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $phpLock->unlock();
            return false;
        }
        $phpLock->unlock();
        return true;
    }
} 