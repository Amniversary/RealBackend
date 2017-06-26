<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-27
 * Time: 下午9:39
 */

namespace frontend\business;


use backend\business\UserUtil;
use common\models\GetCash;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BalanceSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CheckRecordSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\GetCashRecordSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UserActiveSaveByTrans;

class GetCashUtil
{

    /**
     * 设置财务打款信息
     * @param $getCash
     * @param $user_id
     * @param $error
     */
    public static function SetFinaceOk($getCash,$user_id,&$error)
    {
        $error = '';
        //业务日志、修改提现记录状态、给用户消息
        $transActions = [];
        if(!($getCash instanceof GetCash))
        {
            $error = '不是余额提现记录';
            return false;
        }
        if($getCash->status > 2)
        {
            $error = '已经设置打款，无需设置';
            return false;
        }
        $getCash->status = 3;
        $transActions[] = new GetCashRecordSaveByTrans($getCash);
        $user = UserUtil::GetUserByUserId($user_id);
        if(!isset($user))
        {
            $error = '后台人员信息不存在';
            return false;
        }
        $businessLog = BusinessLogUtil::GetBusinessLogNewForBackend(266, $user);
        $businessLog->remark5 = strval($getCash->get_cash_id);
        $businessLog->remark6 = strval($getCash->user_id);
        $businessLog->remark7 = $getCash->nick_name;
        $businessLog->remark9 = sprintf('%s对【%s】进行了提现打款，提现金额【%s】，手续费【%s】，打款金额【%s】，银行卡号【%s】，身份证号【%s】',
          $user->username,
            $getCash->real_name,
            $getCash->cash_money,
            $getCash->cash_fees,
            $getCash->real_cash_money,
            $getCash->card_no,
            $getCash->identity_no);
        $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'财务打款设置业务日志存储失败']);

        $msgContent = sprintf('您的提现金额已经打入尾号【%s】的银行卡，金额【%s】，手续费【%s】，预计算3个工作日内到账，请注意查收！',
            substr($getCash->card_no,strlen($getCash->card_no)-4),
            $getCash->cash_money,
            !isset($getCash->cash_fees)?0:$getCash->cash_fees);
        $msg  = MessageUtil::GetMsgNewModel('74',$msgContent,$getCash->user_id);
        //暂时去掉
        //$transActions[] = new MessageSaveForReward($msg);
        if(!RewardUtil::RewardSaveByTransaction($transActions, $error))
        {
            return false;
        }
        return true;
    }
    /**
     * 根据id获取提现记录
     * @param $cash_id
     * @return null|static
     */
    public static function GetCashRecordById($cash_id)
    {
        return GetCash::findOne(['get_cash_id'=>$cash_id]);
    }

    /**
     * 余额提现
     * @param $passParams
     * @param $user_id
     * @param $error
     * @return bool
     */
    public static function GetCashBYBalance($passParams,$user_id,&$error)
    {
        $error = '';
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户信息不存在';
            return false;
        }
        if(empty($user->centification_level) || $user->centification_level <= 0)
        {
            $error = '提现前请先进行基础认证';
            return false;
        }
        $bank_id = $passParams['bank_id'];
        $bankInfo = UserBankCardUtil::GetCardInfoById($bank_id);
        if(!isset($bankInfo))
        {
            $error = '银行卡信息不存在';
            return false;
        }
        unset($passParams['bank_id']);
        $passParams['identity_no'] = $bankInfo->identity_no;//身份证
        $passParams['real_name']=$bankInfo->user_name;
        $passParams['card_no']=$bankInfo->card_no;
        $passParams['bank_name'] = $bankInfo->bank_name;
        $passParams['status'] = '1';
        $passParams['user_id'] = $user_id;
        $passParams['nick_name'] =$user->nick_name;
        $passParams['create_time']=date('Y-m-d H:i:s');
        $passParams['first_get_money'] = self::IsFirstGetCash($user_id)? 1 : 2;
        $cashModel = self::GetCashNewModel($passParams);
        $msgContent = sprintf('您进行了提现操作，金额【%s】',$passParams['cash_money']);
        $msg = MessageUtil::GetMsgNewModel('66',$msgContent,$user_id);
//        $phpLock = new PhpLock('get_cash_'.strval($user_id));
//        $phpLock->lock();
        $billInfo = PersonalUserUtil::GetUserBillInfoByUserId($user_id);
        if(!isset($billInfo))
        {
            $error = '账户信息不存在';
            return false;
        }
        $cashMoney = doubleval($passParams['cash_money']);
        $billBalance = doubleval($billInfo->balance);
        if($billBalance < $cashMoney)
        {
            $error = '余额不足';
            return false;
        }
        $userActive = UserActiveUtil::GetUserActiveByUserId($user_id);
        if(!isset($userActive))
        {
            $error = '活跃度信息不存在';
            return false;
        }
        $transActions = [];
        $transActions[] = new BalanceSaveForReward($billInfo,['modify_type'=>'get_cash','cash_money'=>$cashMoney]);
        //余额操作日志
        $transActions[] = new CreateUserBalanceLogByTrans($billInfo,['op_money'=>$cashMoney,'operate_type'=>'3']);

        $transActions[] = new UserActiveSaveByTrans($userActive,['modify_type'=>'balance_cash','balance_cash_money'=>$cashMoney]);
        $transActions[] = new GetCashRecordSaveByTrans($cashModel,[]);

        $businessLog = BusinessLogUtil::GetBusinessLogForGetCash($cashModel,$user,$billInfo);
        $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'账户余额提现业务日志存储异常',
            'propertys'=>[
                'remark10'=>[
                    'model'=>'user_bill',
                    'attr'=>'attributes',
                    'key_method'=>'SetRemark10ByUserAccountInfo',
                ],
                'remark5'=>[
                    'model'=>'get_cash',
                    'attr'=>'get_cash_id',
                    'value_php_fun'=>'strval',
                ],
            ]]);

        $checkRecord = BusinessCheckUtil::GetBusinessCheckModelNew(2,'',$user);
        $transActions[] = new CheckRecordSaveForReward($checkRecord,['propertys'=>[
            'relate_id'=>[
                'model'=>'get_cash',
                'attr'=>'get_cash_id',
                'value_php_fun'=>'strval',
            ],
        ]]);
        $transActions[] = new MessageSaveForReward($msg,[]);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 获取新模型
     * @param $attrs
     */
    public static function GetCashNewModel($attrs)
    {
        $model = new GetCash();
        $model->attributes = $attrs;
        return $model;
    }

    /**
     * 判断用户是否存在提现记录
     * @param $user_id
     * @return bool
     */
    public static function IsFirstGetCash($user_id)
    {
        $cash = GetCash::findOne(['user_id'=>$user_id]);
        return $cash == null;
    }
} 