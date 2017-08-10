<?php

use yii\filters\AccessControl;
use yii\filters\VerbFilter;

return [
    'access' => [
        'class' => AccessControl::className(),
        'only' => ['index','customer'],
        'rules' => [
            [
                'actions' => ['index','customer'],
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