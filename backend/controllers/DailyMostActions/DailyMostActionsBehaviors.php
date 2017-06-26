<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/29
 * Time: 16:29
 */

use yii\filters\VerbFilter;
use yii\filters\AccessControl;

return [
    'access' => [
        'class' => AccessControl::className(),
        'only' => ['index','indexgift','indexrecharge'],
        'rules' => [
            [
                'actions' => ['index','indexgift','indexrecharge'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
            'remarkbad'=>['post'],
            'remarkbadshow'=>['get'],
        ],
    ],
];