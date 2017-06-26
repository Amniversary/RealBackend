<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 15:21
 */


return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index','delete','update','create','set_status','reset_pwd','index_son','create_son','delete_son'],
        'rules' => [
            [
                'actions' => ['index','delete','update','create','set_status','reset_pwd','black','index_son','create_son','delete_son'],
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