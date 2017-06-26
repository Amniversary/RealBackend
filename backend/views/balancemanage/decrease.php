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
        'label'=>'蜜播 ID',
        'width'=>'150px',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'蜜播昵称',
        'width'=>'130px',
    ],
    [
        'width'=>'310px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update_bean}&nbsp;&nbsp;{modify_money}&nbsp;&nbsp;{update}&nbsp;&nbsp;{ticket}&emsp;{gift_detail}&nbsp;&nbsp;&nbsp;{delete_ticket}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'modify_money':
                    $url = '/client/update_bean?client_id='.strval($model['client_id']);
                    break;
            }
            return $url;
        },
        'buttons'=>[
            'modify_money'=>function($url,$model)
            {
                return Html::a('减少余额','#',['class'=>'balance-modify','data-url'=>"$url",'data-toggle'=>"modal",'data-target'=>"#multi-modal"]);
            },
        ],
    ],
];
?>
<style>
    .myModal .modal-dialog{
        width: 1200px;
        height: 880px;
        overflow: hidden;
    }
    .myModal .modal-body{
        padding: 0;
    }
    iframe{
        border: none;
        width: 100% !important;
        height: 780px !important;
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
    'id'=>'user-manage-list',
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


\yii\bootstrap\Modal::begin([
        'id' => 'multi-modal',
        'clientOptions' => false,
        'header' => Html::button('确定',['class' => 'btn btn-default','id'=>'set_finance']).' '.Html::button('取消',['aria-hidden'=>'true', 'class' => 'btn btn-default','data-dismiss'=>'modal']),
        'size'=>\yii\bootstrap\Modal::SIZE_SMALL,
    ]
);

echo Html::beginTag('div',['class'=>'mulitremark']);
echo Html::radio('OperateType',false,['id'=>'virtual_bean','value'=>'2']).Html::label('扣除实际豆','virtual_bean',['class'=>'check-item']);
echo '<br/>';
echo Html::radio('OperateType',false,['id'=>'sub_money','value'=>'4']).Html::label('扣除虚拟豆','sub_money',['class'=>'check-item']);
echo '<br/>';
echo Html::radio('OperateType',false,['id'=>'sub_ticket','value'=>'6']).Html::label('扣除可提现票数','sub_ticket',['class'=>'check-item']);
echo '<br/>';
echo '<br/>';
echo Html::label('数量','input_remark',['class'=>'check-item']);
echo Html::input('text','input_money',null,['class'=>'form-control my-input','id'=>'input_money']);
echo Html::endTag('div');
\yii\bootstrap\Modal::end();
echo Html::hiddenInput('IsSubmit','0',['id'=>'IsSubmit']);



$js='
$(".nav-tabs a").click("click",function (e) {
  $(this).parent().addClass("active").siblings().removeClass("active");
})
artDialog.tips = function (content, time) {
    return artDialog({
        id: "Tips",
        title: false,
        cancel: false,
        fixed: true,
        lock: true
    })
    .content("<div style=\"padding: 0 1em;\">" + content + "</div>")
    .time(time || 1);
};
var curUrl = null;
//点击修改余额
$(".content-wrapper").on("click",".balance-modify",function(){
    curUrl = $(this).attr("data-url");
    $("#multi-modal").modal("show");
    return false;
});

$("#set_finance").on("click",function(){
    isSub = $("#IsSubmit").val();
    if(isSub == "1")
    {
        return;
    }

    op = $("input[name=\"OperateType\"]:checked").val();
    money = $("#input_money").val();
    money = money.replace(/(^\s*)|(\s*$)/g, "");
    if(money.length == 0 || isNaN(money))
    {
        artDialog.tips("金额必须是数字");
        return;
    }
    $("#IsSubmit").val("1");
    dataStr = "operate_type="+op+"&op_money="+money;
            $.ajax({
        type: "POST",
        url: curUrl,
        data:dataStr,
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                     $("#user-manage-list").yiiGridView("applyFilter");
                     $("#multi-modal").modal("hide");
                 }
                 else
                 {
                     artDialog.tips(data.msg,2);
                 }
                 $("#IsSubmit").val("0");
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                artDialog.tips("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                $("#IsSubmit").val("0");
             }
        });
});


$(function(){
    $("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
    $("#multi-modal").on("hide.bs.modal",function(e){$(this).removeData();});
});
';
$this->registerJs($js,\yii\web\View::POS_END);