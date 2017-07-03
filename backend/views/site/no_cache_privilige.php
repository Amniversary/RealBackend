<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/6
 * Time: 16:37
 */
$this->title = 'Real数据平台';
echo \yii\bootstrap\Alert::widget([
    'body'=>'您没有选择公众号，无法进行相应操作！',
    'options'=>[
        'class'=>'alert-danger',
    ]
]);