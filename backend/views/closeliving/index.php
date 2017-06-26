<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:48
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'log_id',
        'label' => 'ID',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'living_master_name',
        'label' => '主播昵称',
        'vAlign'=>'middle',
    ],

    [
        'attribute'=>'living_master_no',
        'label' => '主播蜜播号',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'living_before_id',
        'label' => '直播场次',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'backend_user_id',
        'label' => '管理员ID',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'backend_user_name',
        'label' => '管理员名称',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'close_time',
        'label' => '关闭直播时间',
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

echo GridView::widget([
    'id'=>'closeliving_list',
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

$this->registerJs($js,\yii\web\View::POS_END);