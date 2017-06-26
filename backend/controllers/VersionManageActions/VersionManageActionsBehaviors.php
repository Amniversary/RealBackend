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
        'only' => ['index','update','setstatus','create','indexson','createson','updateson','setstatusson','detailson','deleteson','set_version_inner','set_register'],
        'rules' => [
            [
                'actions' => ['index','update','setstatus','create','indexson','createson','updateson','setstatusson','detailson','deleteson','set_version_inner','set_register'],
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