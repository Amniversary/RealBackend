<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/10/19
 * Time: 9:00
 */


return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index','detail','edit','delete','create'],
        'rules' => [
            [
                'actions' => ['index','detail','edit','delete','create'],
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