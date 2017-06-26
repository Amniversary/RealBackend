<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/22
 * Time: 16:41
 */

use yii\filters\VerbFilter;
use yii\filters\AccessControl;

return [
    'access' => [
        'class' => AccessControl::className(),
        'only' => ['index','indexhis','remarkbad','remarkbadshow'],
        'rules' => [
            [
                'actions' => ['index','indexhis','remarkbad','remarkbadshow'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
            'remarkbad'=>['post'],
            'remarkbadshow'=>['get'],
        ],
    ],
];