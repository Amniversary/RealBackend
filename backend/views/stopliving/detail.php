<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 13:55
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;


$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'living_id',
        'vAlign'=>'middle',
        'label'=>'直播间ID',
    ],
    [
        'attribute'=>'client_id',
        'vAlign'=>'middle',
        'label'=>'用户ID',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'呢称',
    ],
    [
        'attribute'=>'manage_id',
        'vAlign'=>'middle',
        'label'=>'操作员ID',
    ],
    [
        'attribute'=>'manage_name',
        'vAlign'=>'middle',
        'label'=>'操作员姓名',
    ],
    [
         'attribute'=>'operate_type',
         'vAlign'=>'middle',
         'label'=>'操作类型',
         'value'=>function($model){
             if ( $model['operate_type'] == 1 ) {
                 return 'App端';
             }else{
                 return '系统后台';
             }
         },

    ],
    [
         'attribute'=>'create_date',
         'label'=>'操作时间',
         'vAlign'=>'middle',
         'width'=>'320px',
    ],

];

echo \yii\bootstrap\Alert::widget([
    'body'=>'搜索的日期格式：yyyy-mm-dd hh:mm:ss|yyyy-mm-dd hh:mm:ss，时间间隔请不要超过15天',
    'options'=>[
        'class'=>'alert-warning',
    ]
]);
?>
<style>
        .alert-warning{
            background-color: #f39c12 !important;
            color: #fff !important;
            padding: 15px;
        }

</style>

<?php          
echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

echo GridView::widget([
    'id'=>'recharge_list',
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
            [
            ],
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


$js = '
$(function(){
$("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
});
';
$this->registerJs($js,\yii\web\View::POS_END);


 function  get_device_type($device_type){
     if( $device_type==1 ){
          return 'android';
     }else if( $device_type == 2 ){
          return 'ios';
     }else if($device_type == 3 ){
          return '其它';
     }
}

function get_operate_type($operate_type){
     if( $operate_type == 1 ){
          return '充值';
     }else if( $operate_type == 2 ){
          return '消费';
     }else if( $operate_type == 3 ){
          return '押注,胜';
     }else if( $operate_type == 4 ){
          return '押注,负';
     }else if( $operate_type == 5 ){
          return '赠送金币';
     }else if( $operate_type == 6 ){
         return '回调(减)';
     }
}