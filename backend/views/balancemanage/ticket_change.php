<style>
    .user-pic
    {
        width: 60px;
    }
    .check-item
    {
        margin-right: 10px;
    }
    .form-control.my-input
    {
        display: inline;
        width: auto;
    }
</style>

<?php

\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;



$gridColumns = [
   [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label'=>'被操作蜜播ID',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'被操作蜜播呢称',
    ],
    [
         'attribute'=>'operate_type',
         'vAlign'=>'middle',
         'label'=>'操作类型',
         'value'=>function($model){
             if ( $model['operate_type'] == 2 ) {
                 return '票转豆';
             }else if ( $model['operate_type'] == 4 ) {
                 return '票提现';
             }else if ( $model['operate_type'] == 7 ) {
                 return '收礼物';
             }else if ( $model['operate_type'] == 13 ) {
                 return '退款';
             }else if ( $model['operate_type'] == 29 ) {
                 return '收门票，票增加';
             }else if ( $model['operate_type'] == 30 ) {
                 return '可提现票数增加';
             }else if ( $model['operate_type'] == 31 ) {
                 return '可提现票数减少';
             }
         },
         'filter'=>['2'=>'票转豆','4'=>'票提现','7'=>'收礼物','13'=>'退款','29'=>'收门票，票增加','30'=>'可提现票数增加','31'=>'可提现票数减少'],
    ],
    [
        'attribute'=>'operate_value',
        'vAlign'=>'middle',
        'label'=>'交易数',
    ],
    [
        'attribute'=>'before_balance',
        'vAlign'=>'middle',
        'label'=>'操作前金额',
    ],
    [
        'attribute'=>'after_balance',
        'vAlign'=>'middle',
        'label'=>'操作后金额',
    ],
    [
        'attribute'=>'account_balance',
        'vAlign'=>'middle',
        'label'=>'操作人用户名',
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'label'=>'操作时间',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
];

echo GridView::widget([
    'id'=>'living_list',
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

$js = '';
$this->registerJs($js,\yii\web\View::POS_END);
?>