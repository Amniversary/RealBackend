<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/31
 * Time: 10:52
 */
return [
    '3'=>[
        'reward'=>'frontend\business\OtherPay\OtherPayResultKinds\AlipayOtherPayResultForReward',
        'recharge'=>'frontend\business\OtherPay\OtherPayResultKinds\AlipayOtherPayResultForRecharge',
        'payback'=>'frontend\business\OtherPay\OtherPayResultKinds\AlipayOtherPayResultForPayBack',
        'prestore'=>'frontend\business\OtherPay\OtherPayResultKinds\AlipayPayResultForPrestore',
    ],
    '4'=>[
        'reward'=>'frontend\business\OtherPay\OtherPayResultKinds\WxpayOtherPayResultForReward',
        'recharge'=>'frontend\business\OtherPay\OtherPayResultKinds\WxpayOtherPayResultForRecharge',
        'payback'=>'frontend\business\OtherPay\OtherPayResultKinds\WxpayOtherPayResultForPayBack',
        'prestore'=>'frontend\business\OtherPay\OtherPayResultKinds\WxpayPayResultForPrestore',
    ],
    '5'=>[
        'reward'=>'frontend\business\OtherPay\OtherPayResultKinds\LlpayOtherPayResultForReward',
        'recharge'=>'frontend\business\OtherPay\OtherPayResultKinds\LlpayOtherPayResultForRecharge',
        'payback'=>'frontend\business\OtherPay\OtherPayResultKinds\LlpayOtherPayResultForPayBack',
    ],
    '6'=>[
        'reward'=>'frontend\business\OtherPay\OtherPayResultKinds\ApplepayOtherPayResultForReward',
        'recharge'=>'frontend\business\OtherPay\OtherPayResultKinds\ApplepayOtherPayResultForRecharge',
        'payback'=>'frontend\business\OtherPay\OtherPayResultKinds\ApplepayOtherPayResultForPayBack',
    ],
    '100'=>[//WebWxpayOtherPayResultForReward
        'reward'=>'frontend\business\OtherPay\OtherPayResultKinds\WebWxpayOtherPayResultForReward',
        'recharge'=>'frontend\business\OtherPay\OtherPayResultKinds\WebWxpayOtherPayResultForRecharge',
        'payback'=>'frontend\business\OtherPay\OtherPayResultKinds\WxpayOtherPayResultForPayBack',
        'recharge_gold'=>'frontend\business\OtherPay\OtherPayResultKinds\WebWxpayOtherGoldPayResultForRecharge',
    ],
];