<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/31
 * Time: 10:55
 */

namespace frontend\business\OtherPay\OtherPayResultKinds;


use backend\business\GoodsUtil;
use common\components\PhpLock;
use common\models\Goods;
use frontend\business\BalanceUtil;
use frontend\business\BusinessLogUtil;
use frontend\business\ClientUtil;
use frontend\business\MessageUtil;
use frontend\business\OtherPay\IOtherPayResult;
use frontend\business\PersonalUserUtil;
use frontend\business\RechargeListUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BalanceSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RechargeListRecordSaveByTrans;
use yii\log\Logger;

class WxpayOtherPayResultForRecharge implements IOtherPayResult
{
    public function DoOtherPayResult($params, &$error)
    {
        $transActions = [];
        if($params['trade_ok'] !== '2')
        {
            //支付失败不做处理
            $error = '余额充值微信支付失败，单号：'.$params['out_trade_no'].' '. var_export($params,true);
            \Yii::getLogger()->log($error, Logger::LEVEL_ERROR);
            return false;
        }
        //获取充值记录、修改余额、生成修改余额业务日志消息、生成消息
        if(!isset($params['charge_id']) || empty($params['charge_id']))
        {
            $error = '微信充值记录id丢失';
            return false;
        }
        if(!isset($params['trade_no']) || empty($params['trade_no']))
        {
            $error = '微信第三方交易单号丢失';
            return false;
        }
        if(!isset($params['out_trade_no']) || empty($params['out_trade_no']))
        {
            $error = '微信订单号丢失';
            return false;
        }
        $trade_no = $params['trade_no'];
        $bill_no = $params['out_trade_no'];
        $charge_id = $params['charge_id'];
        $chargeInfo = RechargeListUtil::GetChargeListById($charge_id);
        $goods = GoodsUtil::GetGoodsById($chargeInfo->goods_id);
        if(!isset($chargeInfo))
        {
            $error = '微信充值记录丢失，数据不完整';
            return false;
        }
        if($chargeInfo->pay_bill !== $bill_no)
        {
            $error = '微信充值订单号不正确';
            \Yii::getLogger()->log($error.' source:'.$chargeInfo->pay_bill.' now:'.$bill_no, Logger::LEVEL_ERROR);
            return false;
        }
        if($chargeInfo->status_result > 1)
        {
            $error = '微信充值订单状态错误，已经处理过';
            return false;
        }
        if(abs(doubleval($chargeInfo->pay_money) - doubleval($params['total_fee'])/100) > 0.01)
        {
            $error = '支付金额不匹配';
            return false;
        }
        $chargeInfo->pay_bill = $bill_no;
        $chargeInfo->status_result = 2;
        $chargeInfo->other_pay_bill = $trade_no;
        $bean_num = $chargeInfo->bean_num + $goods->extra_bean_num;
        $user_id = $chargeInfo->user_id;
        $user = ClientUtil::GetClientById($user_id);
        if(!isset($user))
        {
            $error = '用户信息不存在';
            return false;
        }
        $balance = BalanceUtil::GetUserBalanceByUserId($user_id);
        if(!isset($balance))
        {
            $error = '用户账户余额信息不存在';
            return false;
        }
        $transActions[] = new RechargeListRecordSaveByTrans($chargeInfo,['other_pay_bill'=>$trade_no]);

        $transActions[] = new ModifyBalanceByAddRealBean($balance,['bean_num'=>$bean_num]);
        //余额操作日志
        $transActions[] = new CreateUserBalanceLogByTrans($balance,['op_value'=>$bean_num,
            'operate_type'=>'1',
            'unique_id'=>$trade_no,
            'relate_id'=>$charge_id,
            'field'=>'bean_balance',
            'device_type'=>$params['device_type'],
        ]);


        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }
} 