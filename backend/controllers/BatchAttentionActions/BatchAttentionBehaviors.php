<?php
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

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
];