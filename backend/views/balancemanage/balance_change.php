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
             if ( $model['operate_type'] == 1 ) {
                 return '充值';
             }else if ( $model['operate_type'] == 3 ) {
                 return '票转豆';
             }else if ( $model['operate_type'] == 6 ) {
                 return '送礼物';
             }else if ( $model['operate_type'] == 12 ) {
                 return '发送弹幕';
             }else if ( $model['operate_type'] == 15 ) {
                 return '后台修改增加豆';
             }else if ( $model['operate_type'] == 17 ) {
                 return '发红包';
             }else if ( $model['operate_type'] == 18 ) {
                 return '收红包';
             }else if ( $model['operate_type'] == 19 ) {
                 return '退红包';
             }else if ( $model['operate_type'] == 20 ) {
                 return '后台修改减少豆';
             }else if ( $model['operate_type'] == 21 ) {
                 return '打赏动态红包';
             }else if ( $model['operate_type'] == 27 ) {
                 return '竞猜密码豆减少';
             }else if ( $model['operate_type'] == 28 ) {
                 return '购买门票豆减少';
             }
         },
         'filter'=>['1'=>'充值','3'=>'票转豆','6'=>'送礼物','12'=>'发送弹幕','15'=>'后台修改增加豆','17'=>'发红包','18'=>'收红包','19'=>'退红包','20'=>'后台修改减少豆','21'=>'打赏动态红包','27'=>'竞猜密码豆减少','28'=>'购买门票豆减少'],
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


$this->registerJs($js,\yii\web\View::POS_END);
?>