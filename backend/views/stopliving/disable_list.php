<?php


use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    [
        'attribute'=>'client_no',
        'label' => '被操作蜜播ID',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'nick_name',
        'label' => '被操作密播昵称',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'operate_type',
        'vAlign'=>'middle',
        'label' => '操作来源',
        'value'=>function($model)
        {
            return (($model['operate_type']) == 1 ? '前端':'后台');
        },
        'filter'=>['1'=>'前端','2'=>'后台'],
    ],
    [
        'attribute'=>'manage_id',
        'label' => '操作员用户D',
        'vAlign'=>'middle',

    ],
    [
        'attribute'=>'manage_name',
        'label' => '操作员用户名',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'management_type',
        'vAlign'=>'middle',
        'label' => '操作内容',
        'value'=>function($model)
        {
            return (($model['management_type']) == 1 ? '禁用':'解禁');
        },
        'filter'=>['1'=>'禁用','2'=>'解禁'],
    ],
    [
        'attribute'=>'remark1',
        'vAlign'=>'middle',
        'label' => '操作原因',
    ],
    [
        'attribute'=>'create_time',
        'label' => '创建时间',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],

];

echo GridView::widget([
    'id'=>'close_list',
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
        //[
        //    'content'=> Html::button('新增商品',['type'=>'button','title'=>'新增商品', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('goods/create').'";return false;']),

        //],
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

$js='
$("#goods_list-pjax").on("click",".delete",function(){
    if(!confirm("确定要删除该记录吗？"))
    {
        return false;
    }
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
                    $("#goods_list").yiiGridView("applyFilter");
                 }
                 else
                 {
                     alert("删除失败：" + data.msg);
                     //window.location.reload()
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
             }
        });
        return false;
});
';
$this->registerJs($js,\yii\web\View::POS_END);