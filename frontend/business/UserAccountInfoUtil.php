<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-18
 * Time: 下午10:43
 */

namespace frontend\business;


use common\components\PhpLock;
use common\models\AccountInfo;
use common\models\User;
use common\models\UserAccountInfo;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BalanceSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use yii\log\Logger;

class UserAccountInfoUtil
{
    /**
     * 修改账户余额
     * @param $user_id
     * @param $operate_type
     * @param $op_money
     * @param $error
     */
    public static function ModifyBalance($user_id,$operate_type,$op_money,$op_user,&$error,$unique_id='')
    {
        if(!($op_user instanceof User))
        {
            $error = '操作用户不合法';
            return false;
        }
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!($user instanceof AccountInfo))
        {
            $error = '用户信息不存在';
            return false;
        }

        $userBill = PersonalUserUtil::GetUserBillInfoByUserId($user_id);
        if(!($userBill instanceof UserAccountInfo))
        {
            $error = '账户信息不存在';
            return false;
        }
        $operate_type = intval($operate_type);
        if(!in_array($operate_type,[4,5]))
        {
            $error = '操作类型错误';
            return false;
        }
        $op_money = doubleval($op_money);
        if($op_money <= 0)
        {
            $error ='操作金额不能小于零';
            return false;
        }
        $transActions = [];
        switch($operate_type)
        {
            case 4:
                $transActions[] = new BalanceSaveForReward($userBill,['modify_type'=>'add_balance','add_money'=>$op_money]);
                break;
            case 5:
                $transActions[] = new BalanceSaveForReward($userBill,['modify_type'=>'sub_balance','sub_money'=>$op_money]);
                break;
        }
        //余额操作日志
        $transActions[] = new CreateUserBalanceLogByTrans($userBill,['op_money'=>$op_money,'operate_type'=>$operate_type]);

        $businessModel = BusinessLogUtil::GetBusinessLogNew(271,$user);
        $businessModel->remark5 = strval($userBill->account_info_id);
        $businessModel->remark6 = $op_user->username;
        $businessModel->remark7 = strval($op_user->backend_user_id);
        $businessModel->remark9 = sprintf('后台管理员%s修改了用户【%s】的余额，%s金额【%s】元',$op_user->username,$user->nick_name,($operate_type === 4?'增加':'扣除'),$op_money);
        $transActions[] = new BusinessLogSaveForReward($businessModel,['error'=>'修改金额业务日志存储异常']);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$info,$error))
        {
            return false;
        }
        return true;
    }

    public static function SaveClient($client,&$error)
    {
        if(!($client instanceof AccountInfo))
        {
            $error = '不是用户记录';
            return false;
        }
        if(!$client->save())
        {
            $error = '用户记录保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($client->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 修改用户账户余额记录信息
     * @param $modify_type
     * @param $params
     * @param $billInfo
     * @param $error
     * @return bool
     */
    public static function ModifyUserBillAccountInfo($modify_type,$params,&$billInfo,&$error)
    {
        $error = '';
        if(!($billInfo instanceof UserAccountInfo))
        {
            $error = '不是账户余额记录对象';
            return false;
        }
        $sign = $billInfo->sign;
        $nowSign = PersonalUserUtil::GetUserAccountInfoSign($billInfo->attributes);
        if($sign !== $nowSign)
        {
            $error = '账户信息异常，请与客户人员联系';
            \Yii::getLogger()->log($error.' sign:'.$sign.' nowSign:'.$nowSign, Logger::LEVEL_ERROR);
            return false;
        }
        if(empty($modify_type))
        {
            $error = '修改类型不能为空';
            return false;
        }
        $wishMofiyConfigFile = __DIR__.'/UserAccountBalanceActions/UserAccountModifyConfig.php';
        if(!file_exists($wishMofiyConfigFile))
        {
            $error = '修改账户余额配置文件不存在';
            \Yii::getLogger()->log($error.' file:'.$wishMofiyConfigFile,Logger::LEVEL_ERROR);
            return false;
        }
        $wishModifyConfig = require($wishMofiyConfigFile);
        if(!isset($wishModifyConfig[$modify_type]))
        {
            $error = '修改账户余额类型不正确';
            \Yii::getLogger()->log($error.' modify_type:'.$modify_type,Logger::LEVEL_ERROR);
            return false;
        }
        $wishModifyClass = $wishModifyConfig[$modify_type];
        if(!class_exists($wishModifyClass))
        {
            $error = '修改账户余额类不存在';
            \Yii::getLogger()->log($error.' class:'.$wishModifyClass,Logger::LEVEL_ERROR);
            return false;
        }
        $user_id = $billInfo->user_id;
        $phpLock = new PhpLock('user_bill_account_modify_'.strval($user_id));
        $phpLock->lock();
        $billInfo = PersonalUserUtil::GetUserBillInfoByUserId($user_id);
        try
        {
            $modifyInstance = new $wishModifyClass;
            if(!$modifyInstance->ModifyUserBillInfo($params,$billInfo, $error))
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