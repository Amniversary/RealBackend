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
            return \backend\models\LivingTimeForm::GetIsContractName($model['is_contract']);
        },
        'filter'=>['1'=>'未签约','2'=>'已签约'],
    ],
    [
        'label'=>'日期',
        'attribute'=>'date',
        'vAlign'=>'middle',
        /*'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],*/
    ],
    [
        'label'=>'直播时间(分钟)',
        'attribute'=>'living_second',
        'vAlign'=>'middle',
        'filter'=>false
    ]
];
echo \yii\bootstrap\Alert::widget([
    'body'=>'温馨提示：日期搜索请按照格式搜索，格式为：YYYY-MM-DD 例如：2016-08-20 ，查询某个时间段的日期格式为YYYY-MM-DD_YYYY-MM-DD 例如：2016-03-21_2016-08-20。',
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