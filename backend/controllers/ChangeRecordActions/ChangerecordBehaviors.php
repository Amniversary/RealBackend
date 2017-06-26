<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 16:58
 */

return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index'],
        'rules' => [
            [
                'actions' => ['index'],
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