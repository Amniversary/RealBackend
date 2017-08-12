<?php
use yii\filters\VerbFilter;
use yii\filters\Cors;

return [
    'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
            'apiaction' => ['post'],
        ],
    ],
    'corsFilter' => [
        'class' => Cors::className(),
        'cors' => [
            'Origin' => ['*'],
            'Access-Control-Allow-Origin'=>['*'],
            'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'HEAD', 'OPTIONS'],
            'Access-Control-Request-Headers' => ['*'],
        ],
    ]
];