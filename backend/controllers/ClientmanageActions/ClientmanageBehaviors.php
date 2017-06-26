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
        'only' => ['index','create','delete','setstatus','setinner','modify_balance'],
        'rules' => [
            [
                'actions' => ['index','create','delete','setstatus','setinner','modify_balance'],
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
            'setinner'=>['post'],
            'modify_balance'=>['post'],
        ],
    ],
];