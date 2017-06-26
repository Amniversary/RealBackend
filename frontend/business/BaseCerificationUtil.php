<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-21
 * Time: 下午8:58
 */

namespace frontend\business;


use common\models\BaseCentification;
use yii\base\Exception;

class BaseCerificationUtil
{
    /**
     * 根据身份证号查找初级认证记录
     * @param $identity_no
     * @return null|static
     */
    public static function GetBaseCentificationInfoByIdentiNo($identity_no)
    {
        return BaseCentification::findOne(['identity_no'=>$identity_no]);
    }

    /**
     * 执行初级认证
     * @param $user_id
     * @param $certification_id
     * @param $passParams
     * @param $error
     */
    public static function ExcuteBaseCertification($user_id,$certification_id,$passParams, &$error)
    {
        $schoolName = $passParams['school_name'];
        $userType = $passParams['user_type'];
        $schoolInfo = SchoolInfoUtil::GetSchoolInfoByName($schoolName);
        $addCridtMoney = $userType === '1' ? 100 : 500;
        if(isset($schoolInfo))
        {
            $addCridtMoney += ($userType === '1'? $schoolInfo->student_credit : $schoolInfo->social_credit);
        }

        $cf = self::GetBaseCertificationInfoById($certification_id);
        if(!isset($cf))
        {
            $error = '系统错误，初级认证信息丢失';
            return false;
        }
        if($cf->status === 2)
        {
            $error = '已经认证，无需再认证';
            return false;
        }
        $fund = FundUtil::GetFundByUserId($user_id);
        if(!isset($fund))
        {
            $error = '系统错误，美愿基金记录不存在';
            return false;
        }
        $fund->credit_money = strval(doubleval($fund->credit_money) + doubleval($addCridtMoney));
        $fund->credit_balance += doubleval($addCridtMoney);
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户信息不存在';
            return false;
        }
        $user->centification_level = 1;
        $user->user_type = $userType;

        //检测是否有银行卡信息
        $bankInfo = UserBankCardUtil::GetOneBankCardInfoByUserId($user_id);
        if(!isset($bankInfo))
        {
            $error = '必须添加一张银行卡';
            return false;
        }

        $cf->attributes = $passParams;
        $cf->identity_no = strtoupper($cf->identity_no);
        $cf->status = 2;
        $cf->verify_time = date('Y-m-d H:i:s');
        //自动审核，去除人工审核
        //产生审核记录
        //$checkRecord = BusinessCheckUtil::GetBusinessCheckModelForBaseCertification($certification_id);
        $businessLog = BusinessLogUtil::GetBusinessLogForBaseCertification($addCridtMoney,$user, $fund);
        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            if(!$cf->save())
            {
                \Yii::getLogger()->log(var_export($cf->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('初级认证信息更新失败');
            }
            if(!$user->save())
            {
                \Yii::getLogger()->log(var_export($user->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('用户记录更新失败');
            }
            if(!$fund->save())
            {
                \Yii::getLogger()->log(var_export($fund->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('美愿基金信息更新失败');
            }
            if(!$businessLog->save())
            {
                \Yii::getLogger()->log(var_export($businessLog->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('初级认证业务日志信息存储失败');
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
     * 根据id获取初级认证信息
     * @param $certification_id
     * @return null|static
     */
    public static function GetBaseCertificationInfoById($certification_id)
    {
        $rc = BaseCentification::findOne([
            'base_centification_id'=>$certification_id
        ]);
        return $rc;
    }
    /**
     * 根据用户id获取初级认证信息
     * @param $user_id
     */
    public static function GetBaseCertificationInfoByUserId($user_id)
    {
        $rc = BaseCentification::findOne([
            'user_id'=>$user_id
        ]);
        return $rc;
    }

    /**
     * 获取初级认证信息模型
     * @param $user
     */
    public static function GetBaseCertificationNewModel($user_id)
    {
        $model = new BaseCentification();
        $model->user_id = $user_id;
        $model->entrance_school = '1880-10-01 00:00:00';
        $model->leaving_school = '1880-10-01 00:00:00';
        $model->status = 1;//未审核
        $model->user_name = '';
        $model->identity_no = '';
        $model->school_name = '';
        $model->education = '';
        $model->education_no = '';
        $model->work_unit = '';
        $model->unit_call = '';
        $model->user_type = 1;
        return $model;
    }
} 