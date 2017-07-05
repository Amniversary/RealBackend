<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/6
 * Time: 16:37
 */
$this->title = 'Real数据平台';
echo \yii\bootstrap\Alert::widget([
    'body'=>'您没有选择公众号或公众号令牌已重新刷新，请重新选择公众号！',
    'options'=>[
        'class'=>'alert-danger',
    ]
]);