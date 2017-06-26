<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:33
 */

namespace frontend\business\OtherPay\GetPayParamsKinds;


use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use frontend\business\OtherPay\IGetPayParams;
use common\components\alipay\AlipayUtil;
use frontend\business\RechargeListUtil;
use yii\log\Logger;

class GetAlipayParamsForRecharge implements IGetPayParams
{
    public function GetPayParams($passParam,&$outParams,&$error)
    {
        if(!isset($passParam) || !is_array($passParam))
        {
            $error = '参数异常';
            return false;
        }
        //\Yii::getLogger()->log('支付类型参数2 :'.var_export($passParam,true),Logger::LEVEL_ERROR);
        //检测参数
        $fields = ['user_id','goods_id'];
        $fieldLabels = ['用户id','商品id'];
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
        $goods_id = $passParam['goods_id'];
        $pay_bill = WaterNumUtil::GenWaterNum('ZHF-RG-',true,true,date('Y-m-d'),4);
        $unique_op_no = UsualFunForStringHelper::CreateGUID();
        $rechargeModel = RechargeListUtil::GetRechageListNewModel($goods_id,'3',$pay_bill,$user_id,$unique_op_no);
        if($rechargeModel === false)
        {
            $error = '商品不存在';
            \Yii::getLogger()->log($error.' goods_id:'.$goods_id,Logger::LEVEL_ERROR);
            return false;
        }
        if(!$rechargeModel->save())
        {
            $error = '支付宝充值记录保存失败';
            \Yii::getLogger()->log($error.' '.var_export($rechargeModel->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        $charge_id = $rechargeModel->recharge_id;
        $body = sprintf('pay_target=recharge&charge_id=%s&device_type=%s',
            $charge_id,
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
            'total_fee'=>strval($rechargeModel->pay_money),
            'body'=>$body,
            'it_b_pay'=>'30m',
        ];
        //\Yii::getLogger()->log('支付类型参数3 :'.var_export($outParams,true),Logger::LEVEL_ERROR);
        return true;
    }
} 