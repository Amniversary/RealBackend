<?php

return [
    'verbs' => [
        'class' => \yii\filters\VerbFilter::className(),
        'actions' => [
            'get_user_info' => ['post'],
	    'get_attention_list' =>['post'],
        ],
    ],

];