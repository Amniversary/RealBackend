<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 16:00
 */

return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index','delete','update','create','enroll_index','set_status','enroll_already','prize_record','set_prize_record'],
        'rules' => [
            [
                'actions' => ['index','delete','update','create','set_status','prize_record','set_prize_record'],
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