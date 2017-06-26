<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 11:34
 */

return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index','delete','update','create'],
        'rules' => [
            [
                'actions' => ['index','delete','update','create'],
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