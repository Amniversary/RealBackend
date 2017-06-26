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
        'label'=>'记录ID',
        'width'=>'150px',
        'attribute'=>'record_id',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'统计类型',
        'width'=>'150px',
        'attribute'=>'statistic_type',
        'vAlign'=>'middle',
        'value'=> function($model)
        {
            return ($model->statistic_type == 1?'日':($model->statistic_type == 2?'周':'月'));
        },
        'filter'=>['1'=>'日统计','2'=>'周统计','3'=>'月统计'],
    ],
    [
        'label'=>'总胜场数',
        'width'=>'150px',
        'attribute'=>'win_num',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'总负场数',
        'width'=>'150px',
        'attribute'=>'lose_num',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'是否胜负',
        'width'=>'150px',
        'attribute'=>'is_win',
        'vAlign'=>'middle',
        'value'=> function($model)
        {
            return ($model->is_win == 0?'保本':($model->is_win == 1?'胜':'负'));
        },
        'filter'=>['1'=>'胜','2'=>'负','0'=>'保本'],
    ],
    [
        'label'=>'胜场押注总数',
        'attribute'=>'win_chip_money',
        'width' => '320px',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'负场押注总数',
        'attribute'=>'lose_chip_money',
        'width' => '320px',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'系统押注总筹码数',
        'attribute'=>'system_chip_money',
        'width' => '320px',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'statistic_time',
        'label' => '统计日期',
        'vAlign'=>'middle',
        'width'=>'300px',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
];
echo \yii\bootstrap\Alert::widget([
    'body'=>'搜索的日期格式：日：yyyy-mm-dd 周：yyyy-ww 月：yyyy-mm 单个搜索框输入开始和结束日期用 "|" 分隔；如：2016-11-01|2016-11-07',
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