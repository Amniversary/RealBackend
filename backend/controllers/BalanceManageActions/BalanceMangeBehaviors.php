<?php
/*
 * Created By SublimeText3
 * User: jys
 * Date: 2017/3/21
 * Time: 16:00
 */

return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['increase','decrease','balancechange','ticketchange','gift','info'],
        'rules' => [
            [
                'actions' => ['increase','decrease','balancechange','ticketchange','gift','info'],
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