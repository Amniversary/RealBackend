<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-21
 * Time: 下午10:34
 */

namespace frontend\business;

use common\models\UserIntermediate;
use yii\base\Exception;

class UserIntermediateUtil
{
    /**
     * 根据id获取中级认证信息
     * @param $intermediate_id
     * @return null|static
     */
    public static function GetIntermediateCertificationById($intermediate_id)
    {
        return UserIntermediate::findOne([
            'user_intermediate_id'=>$intermediate_id
        ]);
    }

    /**
     * 执行中级认证
     * @param $intermediate_id
     * @param $user_id
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function ExcuteIntermediteCertification($intermediate_id,$user_id,$passParams,&$error)
    {
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户信息不存在';
            return false;
        }
        if($user->centification_level < 1)
        {
            $error = '请先初级认证';
            return false;
        }
        $userType =$user->user_type;
        $addCridtMoney = ($userType == 1 ? 200 : 500);
        $inInfo = self::GetIntermediateCertificationById($intermediate_id);
        if(!isset($inInfo))
        {
            $error = '系统错误，中级认证信息不存在';
            return false;
        }
        if($inInfo->status == 2)
        {
            $error = '已经认证';
            return false;
        }
        $fund = FundUtil::GetFundByUserId($user_id);
        if(!isset($fund))
        {
            $error = '系统错误，美愿基金记录不存在';
            return false;
        }
        //送货地址必须要有一个
        /*$address = AddressUtil::GetOneAddressByUserId($user_id);
        if(!isset($address))
        {
            $error = '必须要填写一个收货地址';
            return false;
        }*/

        $fund->credit_money = strval(doubleval($fund->credit_money) + doubleval($addCridtMoney));
        $fund->credit_balance += doubleval($addCridtMoney);
        $user->centification_level = 2;

        $inInfo->attributes = $passParams;
        $inInfo->verify_time = date('Y-m-d H:i:s');
        $inInfo->status = 2;
        //无需人工认证
        //审核记录
        //$checkRecord = BusinessCheckUtil::GetBusinessCheckModelForIntermediateCertification($intermediate_id);
        $businessLog = BusinessLogUtil::GetBusinessLogForBaseCertification($addCridtMoney,$user, $fund);

        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            if(!$inInfo->save())
            {
                \Yii::getLogger()->log(var_export($inInfo->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('中级认证信息更新失败');
            }
            if(!$fund->save())
            {
                \Yii::getLogger()->log(var_export($fund->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('美愿基金信息更新失败');
            }
            if(!$user->save())
            {
                \Yii::getLogger()->log(var_export($user->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('用户信息更新失败');
            }
            if(!$businessLog->save())
            {
                \Yii::getLogger()->log(var_export($user->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('业务日志信息更新失败');
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $trans->rollBack();
            return false;
        }
        return true;
    }

    /**
     * 根据用户id获取中级认证信息
     * @param $user_id
     * @return null|static
     */
    public static function GetIntermediateCertificationByUserId($user_id)
    {
        return UserIntermediate::findOne([
            'user_id'=>$user_id
        ]);
    }

    /**
     * 获取中级认证模型
     * @param $user_id
     * @return UserIntermediate
     */
    public static function GetIntermediateNewModel($user_id)
    {
        $model = new UserIntermediate();
        $model->user_id = $user_id;
        $model->status = '1';
        $model->qq = '';
        $model->weixin = '';
        $model->weibo = '';
        $model->contract_name='';
        $model->contract_call = '';
        $model->relatetion = '';
        return $model;
    }
} 