<style>
    .mulitremark
    {
        vertical-align: middle;
    }
    .btn_backmoney
    {
        width: 28px;
        height: 28px;
        background-color: #fff;
        color: #008d4c;
        border: 1px solid;
        border-radius: 3px;
        vertical-align: middle;
        text-align: center;
        font-size: 20px;
        line-height: 25px;
        outline: none;
    }
    .btn_check_all
    {
        width: 28px;
        height: 28px;
        background-color: #fff;
        color: #008d4c;
        border: 1px solid;
        border-radius: 3px;
        vertical-align: middle;
        text-align: center;
        font-size: 20px;
        line-height: 25px;
        outline: none;
    }
    .cancelnote
    {
        font-size: 12pt;
        font-weight:normal;
        margin-left: 10px;
        margin-bottom: 10px;
    }
    .labelremark
    {
        vertical-align: middle;
        display: block;
    }
    .inputremark
    {
        width: 100%;
        height: 150px;
        resize: none;
    }
</style>
<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:38
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;

\common\assets\ArtDialogAsset::register($this);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'class'=>'\yii\grid\CheckboxColumn',
        'checkboxOptions'=>function($model)
        {
            return [
                'value'=>$model['business_check_id'],
            ];
        },
        'name'=>'business_check_id',
    ],
    [
        //'class'=>'kartik\grid\BooleanColumn',
        'label'=>'发起时间',
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            //'attribute'=>'start_time',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'label'=>'审核号',
        'attribute'=>'check_no',
        'vAlign'=>'middle',
        'filter'=>false,
        'width'=>'60px',
    ],
    [
        'label'=>'愿望id',
        'attribute'=>'wish_id',
        'vAlign'=>'middle',
        'width'=>'60px',
    ],
    [
        'label'=>'愿望名称',
        'attribute'=>'wish_name',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'愿望金额',
        'attribute'=>'wish_money',
        'vAlign'=>'middle',
        'width'=>'80px',
        'filter'=>false
    ],
    [
        'label'=>'已筹金额',
        'attribute'=>'ready_reward_money',
        'vAlign'=>'middle',
        'width'=>'150px',
    ],
    [
        'label'=>'奖励金额',
        'attribute'=>'red_packets_money',
        'vAlign'=>'middle',
        'width'=>'150px',
    ],
    [
        'label'=>'手机号',
        'attribute'=>'phone_no',
        'vAlign'=>'middle',
        'width'=>'80px',
    ],
    [
        'label'=>'昵称',
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'姓名',
        'attribute'=>'user_name',
        'vAlign'=>'middle',
        'width'=>'70px',
    ],
    [
        'label'=>'身份证',
        'attribute'=>'identity_no',
        'vAlign'=>'middle',
        'width'=>'90px',
    ],
    [
        'label'=>'内部',
        'attribute'=>'is_inner',
        'value'=>function($model)
        {
            return (($model['is_inner'] == 2)?'是':'否');
        },
        'filter'=>false,
        'width'=>'50px',
    ],
    [
        'width'=>'120px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{check}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { return 'wishmoneytobalancerst?relate_id='.$model['wish_id'].'&check_id='.$model['business_check_id']; },
        'updateOptions'=>['title'=>'审核','label'=>'审核', 'data-toggle'=>false],//tooltip
        'buttons'=>[
            'check' => function ($url, $model, $key) {
                return Html::a('审核',$url,[ 'data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px;']);
            },
        ],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'id'=>'gd_check_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:12px;'], // only set when $responsive = false
    'beforeHeader'=>[
        [
//            'columns'=>[
//                ['content'=>'Header Before 1', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 2', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 3', 'options'=>['colspan'=>3, 'class'=>'text-center warning']],
//            ],
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar' =>  [
        ['content'=>Html::button('批量审核',['class' => 'btn btn-default','data-toggle'=>'modal', 'data-target'=>'#multi-modal']),
            //Html::a('&lt;i class="glyphicon glyphicon-repeat">&lt;/i>', ['grid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
        ],
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
    //'floatHeader' => true,
    //'floatHeaderOptions' => ['scrollingTop' => '300px'],
    //'showPageSummary' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);

echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);
echo Html::hiddenInput('IsSubmit','0',['id'=>'IsSubmit']);
\yii\bootstrap\Modal::begin([
        'id' => 'multi-modal',
        'clientOptions' => false,
        'header' => Html::button('批量通过',['class' => 'btn btn-default setmult','value'=>'1','id'=>'set_finance']).'&nbsp;&nbsp;'.Html::button('批量拒绝',['class' => 'btn btn-default setmult','value'=>'0','id'=>'set_finance']).'&nbsp;&nbsp;'.Html::button('取消',['aria-hidden'=>'true', 'class' => 'btn btn-default','data-dismiss'=>'modal']),
        'size'=>\yii\bootstrap\Modal::SIZE_SMALL,
    ]
);
echo Html::beginTag('div',['class'=>'mulitremark']);
echo Html::button('&nbsp;' , ['id'=>'chooseall','class'=>'btn_check_all']).Html::label('并且取消愿望','chooseall',['class'=>'cancelnote']).'<br>';
echo Html::button('&nbsp;' , ['id'=>'btn_backmoney','class'=>'btn_backmoney']).Html::label('取消愿望后不退款','btn_backmoney',['class'=>'cancelnote']).'<br>';
echo '<input type="hidden" id="cancelwish" value="0">';
echo '<input type="hidden" id="backmoney" value="0">';
echo Html::label('拒绝原因','input_remark',['class'=>'labelremark']);
echo Html::textarea('remark','',['class'=>'inputremark','id'=>'input_remark']);
echo Html::endTag('div');
\yii\bootstrap\Modal::end();

$js = '
$(".btn_check_all").click(function() {
        if ($(".btn_check_all").val() == "全选") {
            //$("input[type=\'checkbox\']:checkbox").each(function() { $(this).prop("checked", false); });
            $(".btn_check_all").val("取消");
            $(".btn_check_all").html("&nbsp;");
            $("#cancelwish").val("0");
        }
        else {
            //$("input[type=\'checkbox\']:checkbox").each(function() { $(this).prop("checked", true); });
            $(".btn_check_all").val("全选");
            $(".btn_check_all").html("✓");
            $("#cancelwish").val("1");
        }
    });
$(".btn_backmoney").click(function() {
        if ($(".btn_backmoney").val() == "全选") {
            //$("input[type=\'checkbox\']:checkbox").each(function() { $(this).prop("checked", false); });
            $(".btn_backmoney").val("取消");
            $(".btn_backmoney").html("&nbsp;");
            $("#backmoney").val("0");
        }
        else {
            //$("input[type=\'checkbox\']:checkbox").each(function() { $(this).prop("checked", true); });
            $(".btn_backmoney").val("全选");
            $(".btn_backmoney").html("✓");
            $("#backmoney").val("1");
        }
    });
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
var dialog = null;
$(document).on("click",".setmult",function(){
    var keys = $("#gd_check_list").yiiGridView("getSelectedRows");
    length = keys.length;
    if(length <= 0)
    {
        artDialog.tips("未选择审核记录");
        return;
    }
    checkRst = $(this).attr("value");
    if(checkRst == "0" && $("#input_remark").val() == "")
    {
        artDialog.tips("拒绝原因必须填写");
        return;
    }
    issubmit = $("#IsSubmit").val();
    if(issubmit == "1")
    {
        return;
    }
    $("#IsSubmit").val("1");
    data="check_rst="+checkRst+"&";
    for(i=0; i < length; i++)
    {
        data += "BusinessCheckIds[]=" +keys[i].toString()+"&";
    }
    remark = $("#input_remark").val();
    data += "refused_reason="+remark;
    data += "&cancel_wish="+$("#cancelwish").val();
    data += "&back_money="+$("#backmoney").val();
    dialog = art.dialog({
        title:"批量审核",
        fixed:true,
        lock:true,
        content:"<img src=\"http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/loading24.gif\" />",
        ok:false,
        cancel:false,
    });
    $.ajax({
        type: "POST",
        url: "/auditmanage/mulitwishmoneytobalance",
        data: data,
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                    if(dialog != null)
                     {
                        dialog.close();
                     }
                     artDialog.tips(data.msg);
                     $("#IsSubmit").val("0");
                     $("#gd_check_list").yiiGridView("applyFilter");
                    $("#multi-modal").modal("hide");
                 }
                 else
                 {
                     $("#IsSubmit").val("0");
                     artDialog.tips(data.msg);
                     if(dialog != null)
                     {
                        dialog.close();
                     }
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                 if(dialog != null)
                 {
                    dialog.close();
                 }
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                $("#IsSubmit").val("0");
             }
        });
});
$(function(){
$("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
});
';
$this->registerJs($js,\yii\web\View::POS_END);