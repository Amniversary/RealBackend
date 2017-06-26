<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/16
 * Time: 13:10
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
\common\assets\ArtDialogAsset::register($this);
$gridColumns = [
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'phone_no',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'status',
        'vAlign'=>'middle',
        'value' => function($model)
        {
            return $model->GetUserStatus();
        },
        'filter'=>['1'=>'正常','0'=>'禁用'],
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'remark2',
        'vAlign'=>'middle',
        'label'=>'操作',
        'width'=>'200px',
        'value'=>function($model)
        {
            if($model['status'] == 1 ){
                return '禁用';
            }else  {
                return '正常';
            }
        },
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'remark2',
                'formOptions'=>['action'=>'/stopliving/disablestatus?client_id='.$model['client_id'].'&status='.$model['status']],
                'size'=>'md',
                'value'=>'',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                'options' => ['placeholder' => '请输入原因'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ]
];

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