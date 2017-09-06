<?php
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

return [
    'access' => [
        'class' => AccessControl::className(),
        'only' => ['index'],
        'rules' => [
            [
                'actions' => ['index'],
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