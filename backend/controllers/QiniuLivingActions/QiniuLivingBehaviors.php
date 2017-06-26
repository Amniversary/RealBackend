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
        'only' => ['index','living_parameters','update','create','delete','client_params','create_client','update_client','delete_client','living_info'],
        'rules' => [
            [
                'actions' => ['index','living_parameters','update','create','delete','client_params','create_client','update_client','delete_client','living_info'],
                'allow' => true,
                'roles' => ['@'],//暂时先关闭
            ],
        ],
    ],
    'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
            'upload_pic'=>['post'],
        ],
    ],
];