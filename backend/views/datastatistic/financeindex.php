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
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/25
 * Time: 17:13
 */
\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    [
        'attribute'=>'client_id',
        'vAlign'=>'middle',
        'label'=>'用户 ID',
        'width'=>'100px',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label'=>'蜜播 ID',
        'width'=>'150px',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'用户昵称',
        'width'=>'130px',
    ],
    [
        'attribute'=>'bean_balance',
        'vAlign'=>'middle',
        'label'=>'实际豆余额',
    ],
    [
        'attribute'=>'virtual_bean_balance',
        'vAlign'=>'middle',
        'label'=>'活动豆余额',

    ],
    [
        'attribute'=>'ticket_count',
        'vAlign'=>'middle',
        'label'=>'可提现剩余票数',
    ],
    [
        'attribute'=>'ticket_real_sum',
        'vAlign'=>'middle',
        'label'=>'可提现总票数',
    ],
    [
        'attribute'=>'ticket_count_sum',
        'vAlign'=>'middle',
        'label'=>'累计票数',

    ],
    [

        'attribute'=>'virtual_ticket_count',
        'vAlign'=>'middle',
        'label'=>'虚拟票数',

    ],
    [
        'attribute'=>'send_ticket_count',
        'vAlign'=>'middle',
        'label'=>'送出总票数',
    ],

    [
        'width'=>'150px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{modify_money}',
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
                return Html::a('修改活动豆','#',['class'=>'balance-modify','data-url'=>"$url",'data-toggle'=>"modal",'data-target'=>"#multi-modal"]);
            },
        ],
    ],
];

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
echo Html::radio('OperateType',true,['id'=>'add_money','value'=>'14']).Html::label('增加活动豆','add_money',['class'=>'check-item']).Html::radio('OperateType',false,['id'=>'sub_money','value'=>'16']).Html::label('扣除活动豆','sub_money',['class'=>'check-item']);
echo '<br/>';
echo '<br/>';
echo Html::label('活动豆','input_remark',['class'=>'check-item']);
echo Html::input('text','input_money',null,['class'=>'form-control my-input','id'=>'input_money']);
echo Html::endTag('div');
\yii\bootstrap\Modal::end();
echo Html::hiddenInput('IsSubmit','0',['id'=>'IsSubmit']);


$js='
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
$(".balance-modify").on("click",function(){
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
    op = $("input[type=\"radio\"]:checked").val();
    money = $("#input_money").val();
    money = money.replace(/(^\s*)|(\s*$)/g, "");
    if(money.length == 0 || isNaN(money))
    {
        artDialog.tips("金额必须是数字");
        return;
    }
    $("#IsSubmit").val("1");
    dataStr = "operate_type="+ op+"&op_money="+money;
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
';
$this->registerJs($js,\yii\web\View::POS_END);