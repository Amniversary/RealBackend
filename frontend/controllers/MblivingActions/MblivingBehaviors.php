<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/3
 * Time: 10:07
 */
return \yii\helpers\ArrayHelper::merge([
    [
        'class' => \yii\filters\Cors::className(),
        'cors' => [
            'Origin' => ['http://test.mblive.cn'],
            'Access-Control-Request-Method' => ['*'],
        ],
    ],
], parent::behaviors());