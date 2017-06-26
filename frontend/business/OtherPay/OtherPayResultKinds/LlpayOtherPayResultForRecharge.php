<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/31
 * Time: 10:55
 */

namespace frontend\business\OtherPay\OtherPayResultKinds;


use common\components\PhpLock;
use frontend\business\BusinessLogUtil;
use frontend\business\MessageUtil;
use frontend\business\OtherPay\IOtherPayResult;
use frontend\business\PersonalUserUtil;
use frontend\business\RechargeListUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BalanceSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RechargeListRecordSaveByTrans;
use yii\log\Logger;

class LlpayOtherPayResultForRecharge implements IOtherPayResult
{
    public function DoOtherPayResult($params, &$error)
    {
        $transActions = [];
        if($params['trade_ok'] !== '2')
        {
            //支付失败不做处理
            $error = '余额充值连连支付失败，单号：'.$params['out_trade_no'].' '. var_export($params,true);
            \Yii::getLogger()->log($error, Logger::LEVEL_ERROR);
            return false;
        }
        //获取充值记录、修改余额、生成修改余额业务日志消息、生成消息
        if(!isset($params['charge_id']) || empty($params['charge_id']))
        {
            $error = '连连充值记录id丢失';
            return false;
        }
        if(!isset($params['trade_no']) || empty($params['trade_no']))
        {
            $error = '连连第三方交易单号丢失';
            return false;
        }
        if(!isset($params['out_trade_no']) || empty($params['out_trade_no']))
        {
            $error = '连连订单号丢失';
            return false;
        }
        $trade_no = $params['trade_no'];
        $bill_no = $params['out_trade_no'];
        $charge_id = $params['charge_id'];
        $pLock = new PhpLock('other_pay_dealwithresult_'.$bill_no);
        $pLock->lock();
        $chargeInfo = RechargeListUtil::GetChargeListById($charge_id);
        if(!isset($chargeInfo))
        {
            $error = '连连充值记录丢失，数据不完整';
            $pLock->unlock();
            return false;
        }
        if($chargeInfo->pay_bill !== $bill_no)
        {
            $error = '连连充值订单号不正确';
            \Yii::getLogger()->log($error.' source:'.$chargeInfo->pay_bill.' now:'.$bill_no, Logger::LEVEL_ERROR);
            $pLock->unlock();
            return false;
        }
        if($chargeInfo->status_result > 1)
        {
            $error = '连连充值订单状态错误，已经处理过';
            $pLock->unlock();
            return false;
        }
        if(abs(doubleval($chargeInfo->pay_money) - doubleval($params['total_fee'])) > 0.01)
        {
            $error = '支付金额不匹配';
            return false;
        }
        $chargeInfo->pay_bill = $bill_no;
        $chargeInfo->status_result = 2;
        $chargeInfo->other_pay_bill = $trade_no;

        $user_id = $chargeInfo->user_id;
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户信息不存在';
            $pLock->unlock();
            return false;
        }
        $billInfo = PersonalUserUtil::GetUserBillInfoByUserId($user_id);
        if(!isset($billInfo))
        {
            $error = '用户账户余额信息不存在';
            $pLock->unlock();
            return false;
        }
        $transActions[] = new RechargeListRecordSaveByTrans($chargeInfo,[]);

        $transActions[] = new BalanceSaveForReward($billInfo,['modify_type'=>'recharge','charge_money'=>$chargeInfo->charge_money]);

        //余额操作日志
        $transActions[] = new CreateUserBalanceLogByTrans($billInfo,['op_money'=>$chargeInfo->charge_money,'operate_type'=>'1']);

        $businessLog = BusinessLogUtil::GetBusinessLogNew('263',$user);
        $businessLog->remark5 = strval($chargeInfo->recharge_id);
        //$businessLog->remark6 = $billRecord->bill_id;
        $businessLog->remark7 = strval($billInfo->account_info_id);
        $businessLog->remark9 = sprintf('%s用连连支付进行了余额充值，充值金额【%s】，充值单子id【%s】,充值前余额【%s】',$user->nick_name,$chargeInfo->charge_money,$charge_id,$billInfo->balance);
        $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'连连支付充值业务日志存储异常',
            'propertys'=>[
                'remark10'=>[
                    'model'=>'user_bill',
                    'attr'=>'attributes',
                    'key_method'=>'SetRemark10ByUserAccountInfo',
                ],
            ]]);


        $msgContent = sprintf('您进行了充值，金额【%s】',$chargeInfo->charge_money);
        $msg  = MessageUtil::GetMsgNewModel(77,$msgContent,$user_id);
        $transActions[] = new MessageSaveForReward($msg,[]);


        if(!RewardUtil::RewardSaveByTransaction($transActions,$error))
        {
            $pLock->unlock();
            return false;
        }
        $pLock->unlock();
        return true;
    }
} 