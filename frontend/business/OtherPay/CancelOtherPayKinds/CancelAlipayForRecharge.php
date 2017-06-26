<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/4
 * Time: 13:12
 */

namespace frontend\business\OtherPay\CancelOtherPayKinds;


use frontend\business\OtherPay\ICancelOtherPay;
use frontend\business\RechargeListUtil;
use yii\log\Logger;

class CancelAlipayForRecharge implements ICancelOtherPay
{
    public function CancelPay($params,&$error)
    {
        if(!isset($params))
        {
            $error = '参数不能为空';
            return;
        }
        if(!isset($params['bill_no']) || empty($params['bill_no']))
        {
            $error ='账单号不能为空';
            return false;
        }
        $bill_no = $params['bill_no'];
        $recharge =RechargeListUtil::GetRechargeInfoByBillNo($bill_no);
        if(!isset($recharge))
        {
            $error = '未找到充值记录取消失败';
            \Yii::getLogger()->log($error.' :'.var_export($params,true),Logger::LEVEL_ERROR);
            return false;
        }
        $sql = 'update mb_recharge set status_result=0 where pay_bill=:bill and status_result=1';
        $rst = \Yii::$app->db->createCommand($sql,[':bill'=>$bill_no])->execute();
        if($rst <= 0)
        {
            $sql = \Yii::$app->db->createCommand($sql,[':bill'=>$bill_no])->rawSql;
            \Yii::getLogger()->log('取消支付失败：'.$sql,Logger::LEVEL_ERROR);
            $error = '取消支付失败';
            return false;
        }
        return true;
    }
} 