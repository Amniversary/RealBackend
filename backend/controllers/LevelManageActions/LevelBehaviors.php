<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/2
 * Time: 16:00
 */

return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index','delete','update','create'],
        'rules' => [
            [
                'actions' => ['index','delete','update','create'],
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