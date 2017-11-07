<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/11/3
 * Time: 下午4:00
 */
use yii\filters\AccessControl;

return [
    'access' => [
        'class' => AccessControl::className(),
        'only' => [''],
        'rules' => [
            [
                'actions' => [''],
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
];