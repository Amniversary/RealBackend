<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 13:43
 */


return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['livingtime','masterprofit','set_living_time','set_valid_date','statistic_balance','living_statistic_time','livingmaster_share','sharesource','living_time_detail','statistic_family'],
        'rules' => [
            [
                'actions' => ['livingtime','masterprofit','set_living_time','set_valid_date','statistic_balance','living_statistic_time','livingmaster_share','sharesource','living_time_detail','statistic_family'],
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