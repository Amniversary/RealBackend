<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 13:43
 */


return [
    'access' => [
        'class' => \yii\filters\AccessControl::className(),
        'only' => ['index','contract','setstatus','setinner','cash_rite','client_finance_index','update_bean','unbindwecat','moneydetail','set_client_type','ticket_detail','set_client_id','client_cover_index','client_robot_index','set_status_normal','living','group_nospeaking'],
        'rules' => [
            [
                'actions' =>['index','contract','setstatus','setinner','cash_rite','client_finance_index','update_bean','unbindwecat','moneydetail','set_client_type','ticket_detail','set_client_id','client_cover_index','client_robot_index','set_status_normal','living','group_nospeaking'],
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