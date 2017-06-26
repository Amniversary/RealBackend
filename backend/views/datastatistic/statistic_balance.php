<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 20:03
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'],

    [
        'attribute'=>'wx_recharge',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'alipay_recharge',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'ios_recharge',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'daily_recharge',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'withdraw',
        'vAlign'=>'middle',
    ],

    [
        'attribute'=>'statistic_time',
        'vAlign'=>'middle',

    ],

];

echo GridView::widget([
    'id'=>'family_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[
        [
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar'=> [
        '{export}',
        '{toggleData}',
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']

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

echo \yii\bootstrap\Modal::widget([
        'id' => 'statistic-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);
