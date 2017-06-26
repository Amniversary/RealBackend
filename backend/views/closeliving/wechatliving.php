<?php


use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'user_name',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'operate_type',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return (($model->operate_type) == 1 ? '开启   ':'关闭');
        },
        'filter'=>['1'=>'开启','2'=>'关闭'],
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
    ],

];

echo GridView::widget([
    'id'=>'wechat_list',
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
$("#wechat_list-pjax").on("click",".delete",function(){
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
                    $("#wechat_list").yiiGridView("applyFilter");
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