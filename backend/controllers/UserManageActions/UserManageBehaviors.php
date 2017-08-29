<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/12
 * Time: 16:24
 */

use yii\filters\VerbFilter;
use yii\filters\AccessControl;

return [
    'access' => [
        'class' => AccessControl::className(),
        'only' => ['index','create','delete','resetpwd','setstatus','setprivilige','getprivilige','set_backend'],
        'rules' => [
            [
                'actions' => ['index','create','delete','resetpwd','setstatus','setprivilige','getprivilige','set_backend'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
            'delete' => ['post'],
            'setstatus'=>['post'],
        ],
    ],
];