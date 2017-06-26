<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:48
 */
\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\html;
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'client_id',
        'vAlign'=>'middle',
        'label' => 'ID',
        'width' => '50px'
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label' => '蜜播ID',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'width' => '80px',
        'label' => '用户名',
    ],
    [
        'attribute'=>'ticket_num',
        'vAlign'=>'middle',
        'label' => '票数',
    ],
    [
        'attribute'=>'cash_type',
        'vAlign'=>'middle',
        'width' => '100px',
        'label' => '支付方式',
        'value'=>function($model)
        {
            return ($model['cash_type'] == '1')?'微信':(($model['cash_type'] == '2')?'支付宝':'其他');
        },
        'filter'=>['1'=>'微信','2'=>'支付宝'],
    ],
    [
        'attribute'=>'fail_status',
        'vAlign'=>'middle',
        'width' => '120px',
        'label' => '状态',
        'value'=>function($model)
        {
            switch($model['fail_status'])
            {
                case 1: $rst = '打款失败';break;
                default: $rst = '未知类型';break;
            }
            return $rst;
        },
        'filter'=>['1'=>'打款失败'],
    ],
    [
        'attribute'=>'real_cash_money',
        'vAlign'=>'middle',
        'width' => '120px',
        'label' => '除手续费金额',
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'label' => '创建时间',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'attribute'=>'check_time',
        'vAlign'=>'middle',
        'label' => '审核时间',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'attribute'=>'finace_ok_time',
        'vAlign'=>'middle',
        'label' => '打款时间',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'attribute'=>'remark2',
        'vAlign'=>'middle',
        'width' => '120px',
        'label' => '打款失败原因',
    ],
    [
        'width'=>'120px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/checkmoneygoods/detailcash?data_type=unpaid&record_id='.strval($model['record_id']);

            }
            return $url;
        },
        'updateOptions'=>['title'=>'查看详情','label'=>'查看详情', 'data-toggle'=>false],
        'buttons'=>[
            'update' => function ($url, $model, $key)
            {
                return Html::a('查看详情',$url,['data-toggle'=>'modal', 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
            },
        ],
    ],
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

echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

$js='
//ajax请求提交数据
function is_ajax (url,data)
{
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        datatype: "json",
        async:false,
        success:function(data)
        {
            $("has_submit").val("0");
            data = $.parseJSON(data);
            if(data.code == 0)
            {
                var Dialog = artDialog({
                    title: "提示",
                    content: "数据提交成功!",
                    okValue:"确定",
                    ok:function()
                    {
                        artDialog.close();
                        window.location.reload();
                    },
                    close:function()
                    {
                        window.location.reload();
                    }
                });
                Dialog.show();
            }
            else
            {
                var log = artDialog({
                    title: "提示",
                    content: "设置失败" + data.msg,
                    okValue: "确定",
                    ok:function()
                    {
                        artDialog.close();
                        window.location.reload();
                    },
                    close:function()
                    {
                        window.location.reload();
                    }
                });

                log.show();
            }
            $("#contact-modal").modal("hide");
            $("#user-manage-list").yiiGridView("applyFilter");
        },
        error: function (XMLHttpRequest, textStatus, errorThrown)
        {
            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
            $("#has_submit").val("0");
        }
    });
}

$(document).on("click",".all_select",function()
{
    if($(this).val()%2==0)
    {
        $(".select_check").prop("checked","checked");
        $(this).val(1).text("取消");
    }
    else
    {
        $(this).val(2).text("全选");
        $(".select_check").removeAttr("checked");
    }

});

var dialog = null;
//批量审核通过
$(document).on("click",".select_pass",function()
{
    length = $(".select_check:checked").length;
        if(length <= 0)
        {
            var dig2 = artDialog
            ({
                title: "提示",
                content: "请至少选择一项",
                okValue: "确 定",
                ok: function ()
                {
                    artDialog.close();
                },
            });
            dig2.show();
            return;
        }
    var flag = false;
    var dig3 = artDialog
    ({
            title: "提示",
            content: "确定审核通过吗？",
            okValue: "确 定",
            ok: function ()
            {
                dig3.close();
                var approve_ids = new Array();
                $(".select_check:checked").each(function(e)
                {
                    approve_ids[e] = $(this).val();
                });
                var str_ids = approve_ids.join("-");
                var datas = "data="+str_ids+"&refused_reason=&check_res=3";
                alert(datas);
                is_ajax("/checkmoneygoods/pay_batch_money",datas);
            },
            cancelValue: "取消",
			cancel: function ()
			{
				dig3.close();
			}
    });
    dig3.show();
});

//批量审核拒绝
$(document).on("click",".select_refuse",function()
{
    length = $(".select_check:checked").length;
        if(length <= 0)
        {
            var d2 = artDialog
            ({
                title: "提示",
                content: "请至少选择一项",
                okValue: "确 定",
                ok: function ()
                {
                    artDialog.close();
                },
            });
            d2.show();
            return;
        }


    var dig4 = artDialog
    ({
            title: "提示",
            content: "确定拒绝吗？",
            okValue: "确 定",
            ok: function ()
            {

                 var dig0 = artDialog
                 ({
                    title: "拒绝原因",
                    content: "<textarea style=\"width: 500px;height: 200px;\" id=\"refuse_check\"></textarea>",
                    okValue: "确 定",
                    ok: function ()
                    {
                        var refused_reason = $("#refuse_check").val();
                        if(refused_reason.trim()=="")
                        {
                            var dig5 = artDialog
                            ({
                                title: "提示",
                                content: "拒绝原因不能为空",
                                okValue: "确 定",
                                ok: function ()
                                {
                                    dig5.close();
                                },
                            });

                            dig5.show();
                            return;
                        }

                        dig0.close();

                        var approve_ids = new Array();
                        $(".select_check:checked").each(function(e)
                        {
                            approve_ids[e] = $(this).val();
                        });
                        var str_ids = approve_ids.join("-");
                        var datas = "data="+str_ids+"&refused_reason="+refused_reason+"&check_res=0";
                        is_ajax("/approvebusinesscheck/checkbatch",datas);
                    },
                });
                dig0.show();
            },
            cancelValue: "取消",
			cancel: function ()
			{
				dig4.close();
			}
        });

        dig4.show();
});

$("#check_goods_delete").on("click",function(){
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
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
             }
        });
});

$(function(){
        $("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
    });
';
$this->registerJs($js,\yii\web\View::POS_END);