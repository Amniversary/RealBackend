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
        'only' => ['updateandroid','share_info_params','user_device_params','sign_reward_params','rate_info_params','statistics_info_params','living_params','heartbeat_params','set_system_params','set_system_title','set_description','set_value1','set_value2','set_value3','create','update_create'],
        'rules' => [
            [
                'actions' => ['updateandroid','share_info_params','user_device_params','sign_reward_params','rate_info_params','statistics_info_params','living_params','heartbeat_params','set_system_params','set_system_title','set_description','set_value1','set_value2','set_value3','create','update_create'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
            'delete' => ['post'],
            'setstatus'=>['post'],
        ],
    ],
];