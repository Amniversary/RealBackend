<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/12
 * Time: 16:24
 */

use yii\filters\VerbFilter;
use yii\filters\AccessControl;

return [
    'access' => [
        'class' => AccessControl::className(),
        'only' => ['index','create','check','checkrst','wishmoneytobalance','wishmoneytobalancerst','checkrstforwishmoney','mulitwishmoneytobalance'],
        'rules' => [
            [
                'actions' => ['index','create','check','checkrst','wishmoneytobalance','wishmoneytobalancerst','checkrstforwishmoney','mulitwishmoneytobalance'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
            'check' => ['get'],
            'checkrst'=>['post'],
        ],
    ],
];