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
        'only' => ['index','delete','close_index','wechat_live'],
        'rules' => [
            [
                'actions' => ['index','delete','close_index','wechat_live'],
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