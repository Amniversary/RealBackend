<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/10/15
 * Time: 15:12
 */

namespace frontend\business\OtherPay\CancelOtherPayKinds;


use frontend\business\OtherPay\ICancelOtherPay;
use frontend\business\GoldsPrestoreUtil;
use yii\log\Logger;

class CancelAlipayForPrestore implements ICancelOtherPay
{
    public function CancelPay($params,&$error)
    {
        if(!isset($params)){
            $error = '参数不能为空';
            return false;
        } 
        if(!isset($params['bill_no']) || empty($params['bill_no'])){
            $error ='账单号不能为空';
            return false;
        }  
        $bill_no = $params['bill_no'];  
        $GoldsPrestore = GoldsPrestoreUtil::GetGoldPrestoreModelByBillNo($bill_no);
        if(!isset($GoldsPrestore)){
            $error = '未找到充值记录取消失败';
            \Yii::getLogger()->log($error.' :'.var_export($params,true),Logger::LEVEL_ERROR);
            return false;
        }
        $sql = 'update mb_golds_prestore set status_result=0 where pay_bill=:bill and status_result=1';
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