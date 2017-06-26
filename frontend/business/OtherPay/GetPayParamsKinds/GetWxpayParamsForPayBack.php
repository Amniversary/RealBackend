<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:33
 */

namespace frontend\business\OtherPay\GetPayParamsKinds;


use common\components\WaterNumUtil;
use frontend\business\BillUtil;
use frontend\business\OtherPay\IGetPayParams;
use common\components\alipay\AlipayUtil;
use yii\log\Logger;

class GetWxpayParamsForPayBack implements IGetPayParams
{
    public function GetPayParams($passParam,&$outParams,&$error)
    {
        if(!isset($passParam) || !is_array($passParam))
        {
            $error = '参数异常';
            return false;
        }
        //检测参数
        $fields = ['bill_id','real_back_money','breach_money','last_breach_money','breach_days'];
        $fieldLabels = ['账单id','实际还款金额','违约金额','持续违约金额','违约天数'];
        $len = count($fields);
        for($i =0; $i <$len; $i ++)
        {
            if(!isset($passParam[$fields[$i]]))
            {
                $error = $fieldLabels[$i].'不能为空';
                return false;
            }
        }

        $real_pay_money = $passParam['real_back_money'];
        $bill_id = $passParam['bill_id'];
        $billInfo = BillUtil::GetBillRecordById($bill_id);
        if(!isset($billInfo))
        {
            $error = '找不到账单信息';
            \Yii::getLogger()->log($error.' bill_id'.$bill_id,Logger::LEVEL_ERROR);
            return false;
        }
        $billInfo->pay_bill = WaterNumUtil::GenWaterNum('ZHF-PB-',true,true,'2015-12-30',4);
        $billInfo->pay_times = intval($billInfo->pay_times) + 1;
        if(!$billInfo->save())
        {
            $error = '账单信息保存失败';
            \Yii::getLogger()->log(var_export($billInfo->getErrors(), true),Logger::LEVEL_ERROR);
            return false;
        }
        $body = sprintf('pay_target=payback&bill_id=%s&real_back_money=%s&breach_money=%s&last_breach_money=%s&breach_days=%s',
            $bill_id,
            $passParam['real_back_money'],
            $passParam['breach_money'],
            $passParam['last_breach_money'],
            $passParam['breach_days']
            );
        $out_trade_no = $billInfo->pay_bill;// $out['bill_no'];
        $aliconfig = AlipayUtil::GetAlipayConfig();
        $pid = $aliconfig['partner'];
        $notify_url = $aliconfig['notify_url'];
        $seller_id = $aliconfig['seller_id'];
        $outParams = [
            'service'=>'mobile.securitypay.pay',
            'partner'=>$pid,
            'out_trade_no'=>$out_trade_no,
            '_input_charset'=>'utf-8',
            'sign_type'=>'RSA',
            'notify_url'=>$notify_url,
            'subject'=>'打赏付款',
            'payment_type'=>'1',
            'seller_id'=>$seller_id,
            'total_fee'=>strval($real_pay_money),
            'body'=>$body,
            'it_b_pay'=>'30m',
        ];
        return true;
    }
} 