<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\CheckMoneyGoodsActions\IndexAction',
    ],
    'detail'=>[
        'class'=>  'backend\controllers\CheckMoneyGoodsActions\DetailAction',
    ],
    'checkrefuse'=>[
        'class'=>  'backend\controllers\CheckMoneyGoodsActions\CheckRefuseAction',
    ],
    'checkpass'=>[
        'class'=>  'backend\controllers\CheckMoneyGoodsActions\CheckPassAction',
    ],
    'indexaudited'=>[
        'class'=>  'backend\controllers\CheckMoneyGoodsActions\IndexAuditedAction',
    ],
    'indexcash'=>[
        'class'=>  'backend\controllers\CheckMoneyGoodsActions\IndexCashAction',
    ],
    'detailcash'=>[
        'class'=>  'backend\controllers\CheckMoneyGoodsActions\DetailCashAction',
    ],
    'playmoney'=>[
        'class'=>  'backend\controllers\CheckMoneyGoodsActions\PlayMoneyAction',
    ],
    'checkbatch'=>[
        'class'=>  'backend\controllers\CheckMoneyGoodsActions\CheckBatchMoneyGoodsActions',
    ],
    'userrecharge'=>[
        'class'=> 'backend\controllers\CheckMoneyGoodsActions\UserRechargeRecordAction',
    ],
    'recharge_detail'=>[
        'class'=> 'backend\controllers\CheckMoneyGoodsActions\RechargeDetailAction',
    ],
    'check_recharge_recode'=>[
        'class'=> 'backend\controllers\CheckMoneyGoodsActions\CheckRechargeRecordAction',
    ],
    //账务打款的支付宝和微信分开两个菜单
    'cash_alipay_paid'=>[
        'class'=> 'backend\controllers\CheckMoneyGoodsActions\IndexCashAliPayPaidAction',
    ],
    'cash_alipay_unpaid'=>[
        'class'=> 'backend\controllers\CheckMoneyGoodsActions\IndexCashAliPayUnpaidAction',
    ],
    'cash_wechat_paid'=>[
        'class'=> 'backend\controllers\CheckMoneyGoodsActions\IndexCashWechatPaidAction',
    ],
    'cash_wechat_unpaid'=>[
        'class'=> 'backend\controllers\CheckMoneyGoodsActions\IndexCashWechatUnpaidAction',
    ],
    'pay_batch_money'=>[
        'class'=> 'backend\controllers\CheckMoneyGoodsActions\PlayBatchMoneyAction',
    ],
    'pay_validate'=>[
        'class'=> 'backend\controllers\CheckMoneyGoodsActions\PayValidateAction',
    ],
    'pay_validate_view'=>[
        'class'=> 'backend\controllers\CheckMoneyGoodsActions\PayValidateViewAction',
    ],
    'cash_fail'=>[
    'class'=> 'backend\controllers\CheckMoneyGoodsActions\IndexCashFailAction', //打款失败列表
]
];