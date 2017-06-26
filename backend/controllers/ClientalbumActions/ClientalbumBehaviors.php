<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 16:30
 */

use yii\filters\VerbFilter;
use yii\filters\AccessControl;

return [
    'access' => [
        'class' => AccessControl::className(),
        'only' => ['index','delete'],
        'rules' => [
            [
                'actions' => ['index','delete'],
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