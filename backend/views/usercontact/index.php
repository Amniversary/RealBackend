<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 16:38
 */

\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;

$gridColumns = [
    [
        'attribute'=>'user_id',
        'vAlign'=>'middle',
        'width'=>'60px',
        'label'=>'用户 ID'
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'width'=>'60px',
        'label'=>'密播 ID'
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'width'=>'60px',
        'label'=>'密播昵称'
    ],
    [
        'attribute'=>'user_name',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'用户姓名'
    ],
    [
        'attribute'=>'phone',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'手机号'
    ],
    [
        'attribute'=>'alipay',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'支付宝账号'
    ],
    [
        'attribute'=>'wx_number',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'微信账号'
    ],
    [
        'attribute'=>'wx_name',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'微信昵称'
    ],
    [
        'attribute'=>'address',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'联系地址'
    ],

];


echo GridView::widget([
    'id'=>'params_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[
        [
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'pjax' => true,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]],
    'panel' => [
        'type' => GridView::TYPE_INFO
    ],

]);

$js='
';
$this->registerJs($js,\yii\web\View::POS_END);




