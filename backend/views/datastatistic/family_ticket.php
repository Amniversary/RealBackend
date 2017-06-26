<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/17
 * Time: 13:10
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
//user_id,client_no','nick_name','date','is_contract','living_second
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'label'=>'家族ID',
        'attribute'=>'family_id',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'家族名称',
        'attribute'=>'family_name',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'收入票数',
        'attribute'=>'income_ticket',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'提现票数',
        'attribute'=>'ticket_to_cash',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'统计日期',
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        /*'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],*/
    ],
];
echo \yii\bootstrap\Alert::widget([
    'body'=>'温馨提示：日期搜索请按照格式搜索，格式为：YYYY-MM-DD 例如：2016-08-20 ，查询某个时间段的日期格式为YYYY-MM-DD|YYYY-MM-DD 例如：2016-08-19|2016-08-20。',
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