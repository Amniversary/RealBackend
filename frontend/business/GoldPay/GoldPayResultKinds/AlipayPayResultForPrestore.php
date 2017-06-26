<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/31
 * Time: 10:55
 */

namespace frontend\business\GoldPay\GoldPayResultKinds;


use backend\business\GoodsUtil;
use common\components\PhpLock;
use frontend\business\BalanceUtil;
use frontend\business\BusinessLogUtil;
use frontend\business\ClientUtil;
use frontend\business\MessageUtil;

use frontend\business\OtherPay\IOtherPayResult;
use frontend\business\GoldPay\IGoldPayResult;

use frontend\business\PersonalUserUtil;
use frontend\business\RechargeListUtil;

use frontend\business\GoldsPrestoreUtil;
use frontend\business\GoldsGoodsUtil;
use frontend\business\GoldsAccountUtil;
use frontend\business\GoldsAccountLogUtil;

use frontend\business\RewardUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BalanceSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RechargeListRecordSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\GoldsPrestoreRecordSaveByTrans;

use yii\log\Logger;
      
class AlipayPayResultForPrestore implements IGoldPayResult
{
    public function DoOtherPayResult($params, &$error)
    {
        $transActions = [];
        if($params['trade_ok'] !== '2')
        {
            //支付失败不做处理
            $error = '余额充值支付宝支付失败，单号：'.$params['out_trade_no'].' '. var_export($params,true);
            \Yii::getLogger()->log($error, Logger::LEVEL_ERROR);
            return false;
        }
        //获取充值记录、修改余额、生成修改余额业务日志消息、生成消息
        if(!isset($params['prestore_id']) || empty($params['prestore_id']))
        {
            $error = '充值记录id丢失';
            return false;
        }
        if(!isset($params['trade_no']) || empty($params['trade_no']))
        {
            $error = '第三方交易单号丢失';
            return false;
        }
        if(!isset($params['out_trade_no']) || empty($params['out_trade_no'])){
            $error = '订单号丢失';
            return false;
        }
        $trade_no = $params['trade_no'];
        $bill_no = $params['out_trade_no'];
        $prestore_id = $params['prestore_id'];  
        $params['other_pay_bill'] = $trade_no;
        
        $GoldPrestoreModel = GoldsPrestoreUtil::GetGoldPrestoreModelById($prestore_id);
        //$chargeInfo = RechargeListUtil::GetChargeListById($charge_id);
        //$goods = GoldsGoodsUtil::GetGoldGoodsModelOne($GoldPrestoreModel->gold_goods_id);//  GoodsUtil::GetGoodsById($chargeInfo->goods_id);
        if(!isset($GoldPrestoreModel))
        {
            $error = '充值记录丢失，数据不完整';
            return false;
        }
        if($GoldPrestoreModel->pay_bill !== $bill_no)
        {
            $error = '支付宝充值订单号不正确';
            \Yii::getLogger()->log($error.' source:'.$GoldPrestoreModel->pay_bill.' now:'.$bill_no, Logger::LEVEL_ERROR);
            return false;
        }
        if($GoldPrestoreModel->status_result > 1)
        {
            $error = '充值订单状态错误，已经处理过';
            return false;
        }
        if(abs(doubleval($GoldPrestoreModel->pay_money) - doubleval($params['total_fee'])) > 0.01)
        {
            \Yii::getLogger()->log('prestore:'.$GoldPrestoreModel->pay_money.'  fee:'.$params['total_fee'],Logger::LEVEL_ERROR);
            $error = '支付金额不匹配';
            return false;
        }
        $user_id = $GoldPrestoreModel->user_id;
        $user = ClientUtil::GetClientById($user_id);
        if(!isset($user))
        {
            $error = '用户信息不存在';
            return false;
        }
        
        $GoldsAccountModel = GoldsAccountUtil::GetGoldsAccountModleByUserId($user_id);
        if(!isset($GoldsAccountModel))
        {
            $error = '用户金币帐户不存在';
            return false;
        }

        $transActions[] = new GoldsPrestoreRecordSaveByTrans($GoldsAccountModel,$params);
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }
} 