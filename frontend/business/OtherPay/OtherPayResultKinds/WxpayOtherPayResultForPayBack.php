<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/31
 * Time: 10:55
 */

namespace frontend\business\OtherPay\OtherPayResultKinds;


use frontend\business\BillUtil;
use frontend\business\BorrowFundUtil;
use frontend\business\BusinessLogUtil;
use frontend\business\MessageUtil;
use frontend\business\OtherPay\IOtherPayResult;
use frontend\business\PersonalUserUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FriendInfoSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RewardListSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UserActiveSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\WishSaveForReward;
use frontend\business\UserActiveUtil;
use frontend\business\WishUtil;
use frontend\business\FriendsUtil;
use yii\log\Logger;

class WxpayOtherPayResultForPayBack implements IOtherPayResult
{
    public function DoOtherPayResult($params, &$error)
    {
        if($params['trade_ok'] !== '2')
        {
            //支付失败不做处理
            $error = '还款微信支付失败，单号：'.$params['out_trade_no'];
            return false;
        }
        //检测参数
        $fields = ['bill_id','real_back_money','breach_money','last_breach_money','breach_days'];
        $fieldLabels = ['账单id','实际还款金额','违约金额','持续违约金额','违约天数'];
        $len = count($fields);
        for($i =0; $i <$len; $i ++)
        {
            if(!isset($fields[$i]))
            {
                $error = $fieldLabels[$i].'不能为空，支付宝还款参数丢失';
                return false;
            }
        }
        //更新还款信息、激活下期账单、如果已经完成，更新借款账单已经还清
        $bill_id = $params['bill_id'];
        $bill = BillUtil::GetBillRecordById($bill_id);
        if(!isset($bill))
        {
            $error = '微信支付还款-账单信息不存在';
            return false;
        }
        if($bill->status === 1)
        {
            $error = '微信支付还款-已经还款，无需再处理';
            \Yii::getLogger()->log($error. ' bill_record_id:'.$bill_id,Logger::LEVEL_ERROR);
            return false;
        }
        $borrowRecord = BorrowFundUtil::GetBorrowFundRecordById($bill->borrow_fund_id);
        if(!isset($borrowRecord))
        {
            $error = '微信支付还款-借款记录不存在';
            return false;
        }
        $user = PersonalUserUtil::GetAccontInfoById($bill->user_id);
        if(!isset($user))
        {
            $error = '微信支付还款-用户信息不存在';
            return false;
        }
        if(!BillUtil::PayBackByAlipay($params,$bill,$borrowRecord,$user,$error))
        {
            return false;
        }

        return true;
    }
} 