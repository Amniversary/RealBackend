<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/11
 * Time: 9:29
 */
use yii\filters\VerbFilter;

return [
    'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
            'doaction' => ['post'],
            'checkserver' => ['post'],
        ],
    ],
];