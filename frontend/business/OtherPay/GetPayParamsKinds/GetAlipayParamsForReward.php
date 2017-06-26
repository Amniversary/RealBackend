<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:33
 */

namespace frontend\business\OtherPay\GetPayParamsKinds;


use frontend\business\OtherPay\IGetPayParams;
use common\components\alipay\AlipayUtil;
use frontend\business\RewardUtil;

class GetAlipayParamsForReward implements IGetPayParams
{
    public function GetPayParams($passParam,&$outParams,&$error)
    {
        if(!isset($passParam) || !is_array($passParam))
        {
            $error = '参数异常';
            return false;
        }
        $out = null;
        if(!RewardUtil::SaveAlipayReward($passParam,$out,$error))
        {
            return false;
        }
        $real_pay_money = $out['real_pay_money'];
        $reward_recrod_id = $out['reward_record_id'];
        $out_trade_no = $out['bill_no'];
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
            'body'=>sprintf('reward_id=%s&pay_target=reward',$reward_recrod_id),
            'it_b_pay'=>'30m',
        ];
        return true;
    }
} 