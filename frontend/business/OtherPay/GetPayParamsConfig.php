<?php
/**
 * 获取参数配置页面
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:27
 */
return [
    '3'=>[
        'reward'=>'frontend\business\OtherPay\GetPayParamsKinds\GetAlipayParamsForReward',
        'recharge'=>'frontend\business\OtherPay\GetPayParamsKinds\GetAlipayParamsForRecharge',
        'payback'=>'frontend\business\OtherPay\GetPayParamsKinds\GetAlipayParamsForPayBack',
        'prestore'=>'frontend\business\OtherPay\GetPayParamsKinds\GetAlipayParamsForPrestore',
    ],
    '4'=>[
        'reward'=>'frontend\business\OtherPay\GetPayParamsKinds\GetWxpayParamsForReward',
        'recharge'=>'frontend\business\OtherPay\GetPayParamsKinds\GetWxpayParamsForRecharge',
        'payback'=>'frontend\business\OtherPay\GetPayParamsKinds\GetWxpayParamsForPayBack',
        'prestore'=>'frontend\business\OtherPay\GetPayParamsKinds\GetWxpayParamsForPrestore',
        'otherprestore'=>'frontend\business\OtherPay\GetPayParamsKinds\GetWxpayParamsForOtherPrestore',
        'otherrecharge'=>'frontend\business\OtherPay\GetPayParamsKinds\GetWxpayParamsForOtherRecharge',
    ],
    /*'5'=>[
        'checkbankcard'=>'frontend\business\OtherPay\GetPayParamsKinds\GetLlpayParamsForCheckBankCard',
        'reward'=>'frontend\business\OtherPay\GetPayParamsKinds\GetLlpayParamsForReward',
        'recharge'=>'frontend\business\OtherPay\GetPayParamsKinds\GetLlpayParamsForRecharge',
        'payback'=>'frontend\business\OtherPay\GetPayParamsKinds\GetLlpayParamsForPayBack',
    ],*/
    '100'=>[//微信支付web版本
        'reward'=>'frontend\business\OtherPay\GetPayParamsKinds\GetWxWebpayParamsForReward',
        'recharge'=>'frontend\business\OtherPay\GetPayParamsKinds\GetWxWebpayParamsForRecharge',
        'payback'=>'frontend\business\OtherPay\GetPayParamsKinds\GetWxWebpayParamsForPayBack',
        'prestore'=>'frontend\business\OtherPay\GetPayParamsKinds\GetWxWebpayParamsForPrestore',
    ],
    '10' => [
        'recharge'=>'frontend\business\OtherPay\GetPayParamsKinds\GetXXPayParamsForRecharge',
        'payback'=>'frontend\business\OtherPay\GetPayParamsKinds\GetXXPayParamsForPayBack',
    ],
    '20' => [
        'recharge'=>'frontend\business\OtherPay\GetPayParamsKinds\GetSwiftpassAliAppParamsForRecharge',
    ],
    '21' => [
        'recharge'=>'frontend\business\OtherPay\GetPayParamsKinds\GetSwiftpassWxAppParamsForRecharge',
    ],
    '22' => [
        'recharge'=>'frontend\business\OtherPay\GetPayParamsKinds\GetSwiftpassTenAppParamsForRecharge',
    ]
];