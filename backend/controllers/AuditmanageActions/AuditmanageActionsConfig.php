<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:31
 */
return [
    'index' => [
        'class' => 'backend\controllers\AuditmanageActions\IndexAction',
    ],
    'create'=>[
        'class' => 'backend\controllers\AuditmanageActions\CreateAction',
    ],
    'check'=>[
        'class' => 'backend\controllers\AuditmanageActions\CheckAction',
    ],
    'checkrst'=>[
        'class' => 'backend\controllers\AuditmanageActions\CheckResultAction',
    ],
    'wishmoneytobalance'=>[
        'class' => 'backend\controllers\AuditmanageActions\IndexWishMoneyToBalanceAction',
    ],
    'wishmoneytobalancerst'=>[
        'class' => 'backend\controllers\AuditmanageActions\WishMoneyToBalanceCheckAction',
    ],
    'checkrstforwishmoney'=>[
        'class'=>'backend\controllers\AuditmanageActions\CheckResultForWishMoneyToBalanceAction',
    ],
    'mulitwishmoneytobalance'=>[
        'class'=>'backend\controllers\AuditmanageActions\CheckResultForMulitWishMoneyToBalanceAction',
    ],
];