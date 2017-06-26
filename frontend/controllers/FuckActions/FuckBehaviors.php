<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/12
 * Time: 16:24
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