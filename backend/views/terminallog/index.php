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
        'attribute'=>'device_type',
        'label' => '设备类型',
        'vAlign'=>'middle',
        'width'=>'100px',
        'value'=>function($model)
        {
            if( $model['device_type'] == 1 ){
                return 'android';
            }else  if( $model['device_type'] == 2 ) {
                return 'ios';
            }else {
                return '其他';
            }
        },
        'filter'=>['1'=>'android','2'=>'ios','3'=>'其他'],
    ],

    [
        'attribute'=>'phone_model',
        'label' => '机型信息',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'action_name',
        'label' => '协议名称',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'os_version',
        'label' => '操作系统版本号',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'error_after_data',
        'label' => '异常发送前数据',
        'vAlign'=>'middle',
    ],

    [
        'attribute'=>'encrypt_data',
        'label' => '加密数据',
        'vAlign'=>'middle',
    ],

    [
        'attribute'=>'encrypt_key',
        'label' => '加密key',
        'vAlign'=>'middle',
    ],

    [
        'attribute'=>'token',
        'label' => 'token',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'result',
        'label' => '返回的结果',
        'vAlign'=>'middle',
    ],

    [
        'attribute'=>'package_name',
        'label' => '包名',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'error_log',
        'label' => '错误日志信息',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'create_time',
        'label' => '时间',
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