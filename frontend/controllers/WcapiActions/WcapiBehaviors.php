<?php
use yii\filters\VerbFilter;

return [
    'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
            'apiaction' => ['post'],
        ],
    ],
];