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
        'only' => [],
        'rules' => [
            [
                'actions' => '',
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
];