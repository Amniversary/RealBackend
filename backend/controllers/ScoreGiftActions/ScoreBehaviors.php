<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 14:54
 */

return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index','update','create','delete','gift_score_index','score_create','score_delete','set_score'],
        'rules' => [
            [
                'actions' => ['index','update','create','delete','gift_score_index','score_create','score_delete','set_score'],
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