<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/6
 * Time: 16:37
 */
$this->title = 'Real数据平台';
echo \yii\bootstrap\Alert::widget([
    'body'=>'您没有权限访问此功能',
    'options'=>[
    'class'=>'alert-danger',
     ]
]);
