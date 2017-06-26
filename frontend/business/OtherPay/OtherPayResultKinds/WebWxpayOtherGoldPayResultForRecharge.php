<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/3
 * Time: 20:52
 */

namespace frontend\business\OtherPay\OtherPayResultKinds;


use frontend\business\ClientGoodsUtil;
use frontend\business\ClientUtil;
use frontend\business\GoldsAccountUtil;
use frontend\business\IntegralAccountUtil;
use frontend\business\OtherPay\IOtherPayResult;
use frontend\business\RechargeListUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\GoldsPrestoreRecordSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\IntegralAccountAddByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\NiuNiuGameGoldsAccountAddByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RechargeListRecordGoldSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RechargeListRecordSaveByTrans;
use yii\log\Logger;

class WebWxpayOtherGoldPayResultForRecharge implements IOtherPayResult
{
    public function DoOtherPayResult($params, &$error)
    {
        $transActions = [];
        //\Yii::getLogger()->log('recharge_gold --->:1',Logger::LEVEL_ERROR);
        if($params['trade_ok'] !== '2')
        {
            //支付失败不做处理
            $error = 'web打赏微信支付失败，单号：'.$params['out_trade_no'];
            \Yii::getLogger()->log($error, Logger::LEVEL_ERROR);
            return false;
        }
        //\Yii::getLogger()->log('recharge_gold --->:2',Logger::LEVEL_ERROR);
        //获取支持记录、愿望记录、生成支持业务日志消息、更新活跃度信息
        if(!isset($params['charge_id']) || empty($params['charge_id']))
        {
            $error = 'web充值记录id丢失';
            return false;
        }
        if(!isset($params['trade_no']) || empty($params['trade_no']))
        {
            $error = 'web微信第三方交易单号丢失';
            return false;
        }
        if(!isset($params['out_trade_no']) || empty($params['out_trade_no']))
        {
            $error = '微信订单号丢失';
            return false;
        }
        //\Yii::getLogger()->log('recharge_gold --->:3',Logger::LEVEL_ERROR);
        $trade_no = $params['trade_no'];
        $bill_no = $params['out_trade_no'];
        $charge_id = $params['charge_id'];
        $chargeInfo = RechargeListUtil::GetChargeGoldListById($charge_id);
        $gold_goods = ClientGoodsUtil::GetGoodsGoldInfoById($chargeInfo->gold_goods_id);
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
        //\Yii::getLogger()->log('recharge_gold --->:4',Logger::LEVEL_ERROR);
        if($chargeInfo->status_result > 1)
        {
            $error = '微信充值订单状态错误，已经处理过';
            return false;
        }
        if(abs(doubleval($chargeInfo->pay_money) - doubleval($params['total_fee'])/100) > 0.01)
        {
            //\Yii::getLogger()->log('rechar:'.$chargeInfo->pay_money.' Fee:'.$params['total_fee'],Logger::LEVEL_ERROR);
            $error = '支付金额不匹配';
            return false;
        }
        //\Yii::getLogger()->log('recharge_gold --->:5',Logger::LEVEL_ERROR);
        $chargeInfo->pay_bill = $bill_no;
        $chargeInfo->status_result = 2;
        $chargeInfo->other_pay_bill = $trade_no;
        $user_id = $chargeInfo->user_id;
        $bean_num = $chargeInfo->gold_goods_num; // + $gold_goods->extra_bean_num;
        $extra_integral_num = $gold_goods->extra_integral_num;
        $user = ClientUtil::GetClientById($user_id);
        if(!isset($user))
        {
            $error = '用户信息不存在';
            return false;
        }
        $gold_balance = GoldsAccountUtil::GetGoldsAccountModleByUserId($user_id);
        if(!isset($gold_balance))
        {
            $error = '用户金币余额信息不存在';
            return false;
        }
        $gold_integral = IntegralAccountUtil::GetIntegralAccountModle($user_id);
        if(!isset($gold_integral))
        {
            $error = '用户账户积分信息不存在';
            return false;
        }
        //\Yii::getLogger()->log('recharge_gold --->:6',Logger::LEVEL_ERROR);
        $data = [
            'gold_account_id'=>$gold_balance->gold_account_id,
            'user_id'=>$user_id,
            'device_type'=>$params['device_type'],
            'operate_type'=>1,
            'operateValue'=>$bean_num,
        ];

        $integral = [
            'integral_account_id'=>$gold_integral->integral_account_id,
            'user_id'=>$user_id,
            'device_type'=>$params['device_type'],
            'operate_type'=>1,
            'operateValue'=>$extra_integral_num
        ];
        //\Yii::getLogger()->log('chargeInfo:'.var_export($chargeInfo,true),Logger::LEVEL_ERROR);
        $transActions[] = new RechargeListRecordGoldSaveByTrans($chargeInfo,['other_pay_bill'=>$trade_no]);
        $transActions[] = new NiuNiuGameGoldsAccountAddByTrans($data,['other_pay_bill'=>$trade_no]);
        if($extra_integral_num > 0)
        {
            $transActions[] = new IntegralAccountAddByTrans($integral);
        }
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }
} 