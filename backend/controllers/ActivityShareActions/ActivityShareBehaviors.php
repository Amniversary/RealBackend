<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28
 * Time: 14:05
 */

return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index','update','create','delete'],
        'rules' => [
            [
                'actions' => ['index','update','create','delete'],
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