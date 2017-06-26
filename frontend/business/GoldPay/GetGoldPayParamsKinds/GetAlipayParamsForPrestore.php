<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:33
 */

namespace frontend\business\GoldPay\GetGoldPayParamsKinds;


use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use frontend\business\GoldPay\IGetGoldPayParams;
use common\components\alipay\AlipayUtil;
use frontend\business\GoldsPrestoreUtil;
//use frontend\business\RechargeListUtil;
use yii\log\Logger;

class GetAlipayParamsForPrestore implements IGetGoldPayParams
{
    public function GetPayParams($passParam,&$outParams,&$error)
    {
        if(!isset($passParam) || !is_array($passParam)){
            $error = '参数异常';
            return false;
        }
     
        //检测参数
        $fields = ['user_id','gold_goods_id'];
        $fieldLabels = ['用户id','金币商品id'];
        $len = count($fields);
        for($i =0; $i <$len; $i ++)
        {
            if(!isset($passParam[$fields[$i]]) && !empty($passParam[$fields[$i]]))
            {
                $error = $fieldLabels[$i].'不能为空';
                return false;
            }
            if(doubleval($passParam[$fields[$i]]) <= 0)
            {
                $error = $fieldLabels[$i].'必须大于0';
                return false;
            }
        }
        $user_id = $passParam['user_id'];
        $gold_goods_id = $passParam['gold_goods_id'];
        $pay_bill = WaterNumUtil::GenWaterNum('ZHF-RG-',true,true,date('Y-m-d'),4);
        $unique_op_no = UsualFunForStringHelper::CreateGUID();
        
        $GoldsPrestoreModel = GoldsPrestoreUtil::GetGoldPrestoreModel($gold_goods_id, 3, $pay_bill, $user_id, $unique_op_no);
        if( $GoldsPrestoreModel === false )
        {
            $error = '金币商品不存在';
            \Yii::getLogger()->log($error.'gold_goods_id:'.$gold_goods_id,Logger::LEVEL_ERROR);
            return false;
        }
        if( !$GoldsPrestoreModel->save() ){  
            $error = '支付宝充值记录保存失败';
            \Yii::getLogger()->log($error.' '.var_export($GoldsPrestoreModel->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        $prestore_id = $GoldsPrestoreModel->prestore_id;
        $body = sprintf('pay_target=prestore&prestore_id=%s&device_type=%s',
            $prestore_id,
            $passParam['device_type']
            );  
        $out_trade_no =$pay_bill;// $out['bill_no'];
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
            'subject'=>'充值付款',
            'payment_type'=>'1',
            'seller_id'=>$seller_id,
            'total_fee'=>strval($GoldsPrestoreModel->pay_money),
            'body'=>$body,
            'it_b_pay'=>'30m',
        ];
        return true;
    }
} 