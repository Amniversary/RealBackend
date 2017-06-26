<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/11
 * Time: 14:40
 */

return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => [],
        'rules' => [
            [
                'actions' => [],
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
