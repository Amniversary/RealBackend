<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/29
 * Time: 16:49
 */

return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index','delete','add','update'],
        'rules' => [
            [
                'actions' => ['index','delete','add','update'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'verbs' => [
        'class' => \yii\filters\VerbFilter::className(),
        'actions' => [

        ],
    ],
];