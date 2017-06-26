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
        'label'=>'签约',
        'attribute'=>'is_contract',
        'vAlign'=>'middle',
        'value' => function($model)
        {
            \Yii::getLogger()->log(var_export($model,true),\yii\log\Logger::LEVEL_ERROR);
            return \backend\models\LivingTimeForm::GetIsContractName($model['is_contract']);
        },
        'filter'=>['1'=>'未签约','2'=>'已签约'],
    ],
    [
        'label'=>'直播月份',
        'attribute'=>'statistic_date',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'label'=>'直播时间(h)',
        'attribute'=>'living_time',
        'vAlign'=>'middle',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'living_time',
                'formOptions'=>['action'=>'/datastatistic/set_living_time?record_id='.strval($model['record_id'])],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'label'=>'有效天数',
        'attribute'=>'valid_date',
        'vAlign'=>'middle',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'valid_date',
                'formOptions'=>['action'=>'/datastatistic/set_valid_date?record_id='.strval($model['record_id'])],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
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

$js='
$("#goods_delete").on("click",function(){
    $url = $(this).attr("href");
            $.ajax({
        type: "POST",
        url: $url,
        data: "",
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                     $("#user-manage-list").yiiGridView("applyFilter");
                 }
                 else
                 {
                     alert("删除失败：" + data.msg);
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
             }
        });
});
';
$this->registerJs($js,\yii\web\View::POS_END);