<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */


return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index','detail','checkrefuse','checkpass','indexaudited','indexcash','detailcash','playmoney','checkbatch','userrecharge','check_recharge_recode','cash_alipay_paid','cash_alipay_unpaid','cash_wechat_paid','cash_wechat_unpaid'],
        'rules' => [
            [
                'actions' => ['index','detail','checkrefuse','checkpass','indexaudited','indexcash','detailcash','playmoney','checkbatch','userrecharge','check_recharge_recode','cash_alipay_paid','cash_alipay_unpaid','cash_wechat_paid','cash_wechat_unpaid'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'verbs' => [
        'class' => \yii\filters\VerbFilter::className(),
        'actions' => [

        ],
    ],
];