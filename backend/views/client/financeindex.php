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
        'label'=>'蜜播昵称',
        'width'=>'130px',
    ],
    [
        'attribute'=>'bean_balance',
        'vAlign'=>'middle',
        'label'=>'鲜花余额',
    ],
    [
        'attribute'=>'virtual_bean_balance',
        'vAlign'=>'middle',
        'label'=>'虚拟鲜花余额',

    ],
    [
        'attribute'=>'ticket_count',
        'vAlign'=>'middle',
        'label'=>'可提现剩余票数',
    ],
    [
        'attribute'=>'ticket_real_sum',
        'vAlign'=>'middle',
        'label'=>'实际提现总票数',
    ],
    [
        'attribute'=>'ticket_count_sum',
        'vAlign'=>'middle',
        'label'=>'累计总票数(包含虚拟礼物)',

    ],
    [
        'attribute'=>'virtual_ticket_count',
        'vAlign'=>'middle',
        'label'=>'收到虚拟礼物票数',

    ],
    [
        'attribute'=>'send_ticket_count',
        'vAlign'=>'middle',
        'label'=>'送出礼物总票数',
    ],
    /*[
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'bean_status',
        'vAlign'=>'middle',
        'label'=>'鲜花状态',
        'value'=>function($model)
        {
            if($model['bean_status'] == '1' ){
                return '未冻结';
            }else  {
                return '已冻结';
            }
        },
        'editableOptions'=>function($model)
        {
            return [
                'header' =>'冻结状态',
                'name'=>'bean_status',
                'formOptions'=>['action'=>'/client/set_freeze?user_id='.strval($model['client_id'])],
                'size'=>'sm',
                'value'=>$model['bean_status'],
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'解冻鲜花','2'=>'冻结鲜花'],
            ];
        },
        'filter'=>['1'=>'解冻鲜花','2'=>'冻结鲜花'],
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'ticket_status',
        'vAlign'=>'middle',
        'label'=>'余额状态',
        'value' => function($model)
        {
            if($model['ticket_status'] == '2' ){
                return '已冻结';
            }else  {
                return '未冻结';
            }
        },
        'editableOptions'=>function($model)
        {
            return [
                'header' =>'冻结状态',
                'name'=>'ticket_status',
                'formOptions'=>['action'=>'/client/set_freeze?user_id='.strval($model['client_id'])],
                'size'=>'sm',
                'value'=>$model['ticket_status'],
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'解冻余额','2'=>'冻结余额'],
            ];
        },
        'filter'=>['1'=>'解冻余额','2'=>'冻结余额'],
        'refreshGrid'=>true,
    ],*/
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'freeze_status',
        'vAlign'=>'middle',
        'label'=>'冻结状态',
        'value' => function($model)
        {
            $rst = '';
            switch($model['freeze_status']){
                case '1':
                    $rst = '否';
                    break;
                case '2':
                    $rst = '是';
                    break;
            }
            return $rst;
        },
        'editableOptions'=>function($model)
        {
            return [
                'header' =>'冻结状态',
                'name'=>'freeze_status',
                'formOptions'=>['action'=>'/client/set_freeze?user_id='.strval($model['client_id'])],
                'size'=>'sm',
                'value'=>$model['freeze_status'],
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'否','2'=>'是'],
            ];
        },
        'filter'=>['1'=>'否','2'=>'是'],
        'refreshGrid'=>true,
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
                case 'update':
                    $url = '/client/moneydetail?user_id='.strval($model['client_id']);
                    break;
                case 'ticket':
                    $url = '/client/ticket_detail?user_id='.strval($model['client_id']);
                    break;
                case 'gift_detail':
                    $url = '/client/gift_detail?user_id='.strval($model['client_id']);
                    break;
                /*case 'delete_ticket':
                    $url = '/client/delete_ticket?client_id='.strval($model['client_id']);
                    break;*/
            }
            return $url;
        },
        'buttons'=>[
            'modify_money'=>function($url,$model)
            {
                return Html::a('修改余额','#',['class'=>'balance-modify','data-url'=>"$url",'data-toggle'=>"modal",'data-target'=>"#multi-modal"]);
            },
            'update' => function ($url, $model, $key)
            {
                //return Html::a('余额详情',$url,['data-toggle'=>'modal', 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
                return Html::a('余额详情','javascript:;',['class'=>'money_detail','data-src'=>$url]);
            },
            'ticket' => function ($url, $model, $key)
            {
                //return Html::a('票详情',$url,['data-toggle'=>'modal', 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
                return Html::a('票详情','javascript:;',['class'=>'piao_detail','data-src'=>$url]);
            },
            'gift_detail' => function ($url, $model, $key)
            {
                return Html::a('礼物详情','javascript:;',['class'=>'gift_detail','data-userid'=>$model['client_id']] );
            },
            /*'delete_ticket' => function ($url, $model, $key)
            {
                return Html::a('提现剩余票数清除','javascript:;',['class'=>'delete_ticket','style'=>'color:#f00','rel'=>$url]);
            },*/
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
<!-- 余额情Modal -->
<div class="modal fade myModal" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <iframe id="myFrame3" name="myFrame3" style="width:900px; height:500px;"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- 票详情Modal -->
<div class="modal fade myModal" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <iframe id="myFrame2" name="myFrame2" style="width:900px; height:500px;"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- 礼物详情Modal -->
<div class="modal fade myModal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <ul class="nav nav-tabs">
                        <li role="presentation" class="active"><a href="javascript:;" target="myFrame" id="j_send_gift">送礼物</a></li>
                        <li role="presentation"><a href="javascript:;" target="myFrame" id="j_receive_gift">收礼物</a></li>
                    </ul>

                </h4>
            </div>
            <div class="modal-body">
                <iframe id="myFrame" name="myFrame" style="width:900px; height:500px;"></iframe>
            </div>
        </div>
    </div>
</div>
<?php
echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

echo \yii\bootstrap\Alert::widget([
    'body'=>'余额详情指：实际豆详情；票详情指：可提现剩余票详情',
    'options'=>[
        'class'=>'alert-warning',
    ]
]);

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
echo Html::radio('OperateType',true,['id'=>'charge_type','value'=>'1']).Html::label('增加实际豆','real_bean',['class'=>'check-item']).Html::radio('OperateType',false,['id'=>'charge_type','value'=>'2']).Html::label('扣除实际豆','virtual_bean',['class'=>'check-item']);
echo '<br/>';
echo Html::radio('OperateType',false,['id'=>'add_money','value'=>'3']).Html::label('增加虚拟豆','add_money',['class'=>'check-item']).Html::radio('OperateType',false,['id'=>'sub_money','value'=>'4']).Html::label('扣除虚拟豆','sub_money',['class'=>'check-item']);
echo '<br/>';
echo Html::radio('OperateType',true,['id'=>'add_ticket','value'=>'5']).Html::label('增加可提现票数','real_bean',['class'=>'check-item']).Html::radio('OperateType',false,['id'=>'sub_ticket','value'=>'6']).Html::label('扣除可提现票数','virtual_bean',['class'=>'check-item']);
echo '<br/>';
echo '<br/>';
echo Html::label('数量','input_remark',['class'=>'check-item']);
echo Html::input('text','input_money',null,['class'=>'form-control my-input','id'=>'input_money']);
echo Html::endTag('div');
\yii\bootstrap\Modal::end();
echo Html::hiddenInput('IsSubmit','0',['id'=>'IsSubmit']);



$js='
//余额详情
$(document).on("click", ".money_detail", function(){
    var src = $(this).data("src");
    $("#myModal3").modal("show");
    $("#myFrame3").attr("src", src);
})
//票详情
$(document).on("click", ".piao_detail", function(){
    var src = $(this).data("src");
    $("#myModal2").modal("show");
    $("#myFrame2").attr("src", src);
})
//点击礼物详情
$(document).on("click", ".gift_detail", function(){
    $("#j_send_gift").click();
    var userid = $(this).data("userid");
    var str1 = "/client/send_gift_detail" + "?user_id=" + userid;
    var str2 = "/client/receive_gift_detail" + "?user_id=" + userid;
    $("#j_send_gift").attr("href", str1);
    $("#j_receive_gift").attr("href", str2);
    $("#myModal").modal("show");
    $("#myFrame").attr("src", str1);
})
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


$("body").on("click",".delete_ticket",function(){
    $url = $(this).attr("rel");

    art.dialog.confirm("你确定要删除这个用户的剩余票数吗？", function () {
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
    }, function () {
    });

});
';
$this->registerJs($js,\yii\web\View::POS_END);