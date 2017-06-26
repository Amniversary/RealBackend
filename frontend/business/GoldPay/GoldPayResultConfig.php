<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/31
 * Time: 10:52
 */
return [
    '3'=>[
        'prestore'=>'frontend\business\GoldPay\GoldPayResultKinds\AlipayPayResultForPrestore',
    ],
    '4'=>[
        'prestore'=>'frontend\business\GoldPay\GoldPayResultKinds\WxpayPayResultForPrestore',
    ],
    '6'=>[
        'prestore'=>'frontend\business\GoldPay\GoldPayResultKinds\ApplepayPayResultForRecharge',
    ],
    '100'=>[//WebWxpayOtherPayResultForReward
        'prestore'=>'frontend\business\GoldPay\GoldPayResultKinds\WebWxpayPayResultForPrestore',
    ],
];