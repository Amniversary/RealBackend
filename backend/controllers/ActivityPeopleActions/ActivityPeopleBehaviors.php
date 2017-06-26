<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/26
 * Time: 21:06
 */
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

return [
    'access' => [
        'class' => AccessControl::className(),
        'only' => ['index','create','delete'],
        'rules' => [
            [
                'actions' => ['index','create','delete'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
        ],
    ],
];