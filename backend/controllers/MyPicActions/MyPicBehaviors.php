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
        'only' => ['upload_pic','upload_video'],
        'rules' => [
            [
                'actions' => ['upload_pic','upload_video'],
                'allow' => true,
                'roles' => ['@'],//暂时先关闭
            ],
        ],
    ],
    'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
            'upload_pic'=>['post'],
            'upload_video'=>['post'],
        ],
    ],
];