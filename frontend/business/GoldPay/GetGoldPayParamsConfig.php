<?php
/**
 * 获取参数配置页面
 * Created by PhpStorm.
 * User: wangwei
 * Date: 2016/10/14
 * Time: 10:27
 */
return [
    '3'=>[
        'prestore'=>'frontend\business\GoldPay\GetGoldPayParamsKinds\GetAlipayParamsForPrestore',
    ],
    '4'=>[
        'prestore'=>'frontend\business\GoldPay\GetGoldPayParamsKinds\GetWxpayParamsForPrestore',
    ],
    /*'5'=>[
        'checkbankcard'=>'frontend\business\OtherPay\GetPayParamsKinds\GetLlpayParamsForCheckBankCard',
        'reward'=>'frontend\business\OtherPay\GetPayParamsKinds\GetLlpayParamsForReward',
        'recharge'=>'frontend\business\OtherPay\GetPayParamsKinds\GetLlpayParamsForRecharge',
        'payback'=>'frontend\business\OtherPay\GetPayParamsKinds\GetLlpayParamsForPayBack',
    ],*/
    '100'=>[//微信支付web版本
        'reward'=>'frontend\business\GoldPay\GetPayParamsKinds\GetWxWebpayParamsForReward',
        'recharge'=>'frontend\business\GoldPay\GetPayParamsKinds\GetWxWebpayParamsForRecharge',
        'payback'=>'frontend\business\GoldPay\GetPayParamsKinds\GetWxWebpayParamsForPayBack',
    ],
];