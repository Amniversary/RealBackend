<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/17
 * Time: 13:10
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'label'=>'蜜播号',
        'attribute'=>'client_no',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'昵称',
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'开播次数',
        'attribute'=>'living_before_id',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'开播时间',
        'attribute'=>'create_time',
        'width' => '320px',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'结束时间',
        'attribute'=>'finish_time',
        'width' => '320px',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'直播时间(h)',
        'attribute'=>'living_time',
        'vAlign'=>'middle',
        'filter'=>false
    ]
];
echo \yii\bootstrap\Alert::widget([
    'body'=>'搜索的日期格式：yyyy-mm-dd或yyyy-mm-dd H:i:s；单个搜索框输入开始和结束日期用 "|" 分隔；日期格式输入错误或开始和结束时间为空时默认查询当天时间0点到当前时间',
    'options'=>[
        'class'=>'alert-warning',
    ]
]);
echo GridView::widget([
    'id'=>'goods_list',
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

$this->registerJs($js,\yii\web\View::POS_END);