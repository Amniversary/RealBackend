<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/20
 * Time: 21:08
 */


return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['hot_living','living_status','look_living','set_status','set_order','living_hot'],
        'rules' => [
            [
                'actions' => ['hot_living','living_status','look_living','set_status','set_order','living_hot'],
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