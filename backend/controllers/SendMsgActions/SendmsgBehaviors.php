<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/12
 * Time: 16:45
 */
return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['send','index','upload'], //'index',
        'rules' => [
            [
                'actions' => ['send','index','upload'],
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