<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/31
 * Time: 10:55
 */

namespace frontend\business\OtherPay\OtherPayResultKinds;


use backend\business\GoodsUtil;
use frontend\business\BalanceUtil;
use frontend\business\BusinessLogUtil;
use frontend\business\ChatUtilHuanXin;
use frontend\business\ClientUtil;
use frontend\business\MessageUtil;
use frontend\business\OtherPay\IOtherPayResult;
use frontend\business\PersonalNewStatisticUtil;
use frontend\business\PersonalUserUtil;
use frontend\business\RechargeListUtil;
use frontend\business\RedPacketsUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FriendInfoSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\PersonalRedPacketsSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RechargeListRecordSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RewardListSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UserActiveSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\WishSaveForReward;
use frontend\business\UserActiveUtil;
use frontend\business\WishNewStatisticUtil;
use frontend\business\WishUtil;
use frontend\business\FriendsUtil;
use yii\log\Logger;

class WebWxpayOtherPayResultForRecharge implements IOtherPayResult
{
    public function DoOtherPayResult($params, &$error)
    {
        $transActions = [];
        if($params['trade_ok'] !== '2')
        {
            //支付失败不做处理
            $error = 'web打赏微信支付失败，单号：'.$params['out_trade_no'];
            \Yii::getLogger()->log($error, Logger::LEVEL_ERROR);
            return false;
        }
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
            //\Yii::getLogger()->log('rechar:'.$chargeInfo->pay_money.' Fee:'.$params['total_fee'],Logger::LEVEL_ERROR);
            $error = '支付金额不匹配';
            return false;
        }
        $chargeInfo->pay_bill = $bill_no;
        $chargeInfo->status_result = 2;
        $chargeInfo->other_pay_bill = $trade_no;
        $user_id = $chargeInfo->user_id;
        $bean_num = $chargeInfo->bean_num + $goods->extra_bean_num;
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