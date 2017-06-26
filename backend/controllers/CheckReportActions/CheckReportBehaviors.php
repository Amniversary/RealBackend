<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */


return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index','detail','checkrefuse','checkpass','indexaudited','indexcash','detailcash','playmoney','set_status'],
        'rules' => [
            [
                'actions' => ['index','detail','checkrefuse','checkpass','indexaudited','indexcash','detailcash','playmoney','set_status'],
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