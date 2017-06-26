<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/17
 * Time: 13:10
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
\common\assets\ArtDialogAsset::register($this);
$gridColumns = [
    [
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{select_all}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'header' => '选择',
        'buttons'=>[
            'select_all' => function ($url, $model, $key)
            {
                return Html::checkbox(false,'',['class'=>'select_check','value'=>strval($model->client_id)]);
            },
        ],
    ],
    [
        'attribute'=>'client_id',
        'vAlign'=>'middle',
        'width'=>'80px',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'register_type',
        'vAlign'=>'middle',
        'width'=>'100px',
        'value'=> function($model)
        {
            return $model->GetRegisterType();
        },
        'filter'=>['1'=>'手机','2'=>'微信','3'=>'微博','4'=>'QQ'],

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
        'attribute'=>'is_inner',
        'vAlign'=>'middle',
        'label' =>'内部',
        'value'=>function($model)
        {
            return (($model->is_inner == '1')? '否':'是');
        },
        'filter'=>['1'=>'否','2'=>'是'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/client/setinner?client_id='.strval($model->client_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'否','2'=>'是'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'client_type',
        'vAlign'=>'middle',
        'width'=>'100px',
        'value'=>function($model)
        {
            return $model->GetUserTypeName();
        },
        'filter'=>['1'=>'普通','2'=>'超管'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/client/set_client_type?client_id='.strval($model->client_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'普通','2'=>'超管'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'phone_no',
        'vAlign'=>'middle',
        'format'=>'raw',
        'value' => function($model)
        {
            if(empty($model->phone_no))
            {
                $phone_no = '';
                return $phone_no;
            }
            else{
                $phone_no = strval($model->phone_no);
                $phone_no = substr($phone_no, 0, 3).'****'.substr($phone_no, 7);
                return Html::button($phone_no,['data-phone'=>strval($model->phone_no),'style'=>'background-color: transparent;border: none;color:#428bca;','class'=> 'btn_phone']);
            }
        },
    ],
    [
        'attribute'=>'device_no',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'sex',
        'vAlign'=>'middle',
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'is_contract',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return $model->GetIsContract();
        },
        'filter'=>['1'=>'未签约','2'=>'已签约'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/client/contract?client_id='.strval($model->client_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'未签约','2'=>'已签约'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'cash_rite',
        'vAlign'=>'middle',
        'width'=>'100px',
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/client/cash_rite?client_id='.strval($model->client_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        }
    ],
    [
        'attribute'=>'is_bind_weixin',
        'vAlign'=>'middle',
        'format' =>'raw',
        'value'=>function($model)
        {
            if($model->is_bind_weixin == '2'){
                return Html::button('解绑微信',['data-type'=>1,'style'=>'background-color: transparent;border: none;color:#428bca;','class' => 'btn btn-default all_select unbindWeCat','value'=>'2','data-client-id'=>$model->client_id]);
            }else{
                return '未绑定';
            }

        },
        'filter'=>['1'=>'未绑定','2'=>'绑定'],
    ],
    [
        'attribute'=>'is_bind_alipay',
        'vAlign'=>'middle',
        'width'=>'100px',
        'format'=>'raw',
        'value'=>function($model)
        {
            if($model->is_bind_alipay == '2'){
                return Html::button('解绑支付宝',['data-type'=>2,'style'=>'background-color: transparent;border: none;color:#428bca;','class' => 'btn btn-default all_select unbindWeCat','value'=>'2','data-client-id'=>$model->client_id]);
            }else{
                return '未绑定';
            }

        },
        'filter'=>['1'=>'未绑定','2'=>'绑定'],
    ],

    [
        'attribute'=>'is_centification',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'认证类型',
        'value'=>function($model)
        {
            switch($model->is_centification)
            {
                case 1:
                    $type = '未认证';
                    break;
                case 2:
                    $type = '高级认证';
                    break;
                case 3:
                    $type = '高级认证审核';
                    break;
                case 4:
                    $type = '低级认证';
                    break;
                case 5:
                    $type = '低级认证审核';
                    break;
            }
            return $type;
        },
        'filter'=>['1'=>'未认证','2'=>'高级认证','3'=>'高级认证审核','4'=>'低级认证','5'=>'低级认证审核'],
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
    [
        'width'=>'150px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update_client_id}&nbsp;&nbsp;{status_normal}&nbsp;&nbsp;{status_disable}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update_client_id':
                    $url = '/client/set_client_id?client_id='.strval($model->client_id);
                    break;
                case 'status_normal':
                    $url = '/client/set_status_normal?client_id='.strval($model->client_id);
                    break;
                case 'status_disable':
                    $url = '/client/setstatus?client_id='.strval($model->client_id);
                    break;
            }
            return $url;
        },
        'buttons'=>[
            'update_client_id'=>function($url,$model)
            {
                return Html::a('交换用户账号','#',['class'=>'balance-modify','data-url'=>"$url",'data-toggle'=>"modal",'data-target'=>"#multi-modal"]);
            },
            'status_normal'=>function($url,$model)
            {
                return Html::a('正常',$url,['title' => '正常', 'class' => 'normal', 'data-toggle' => false]);
            },
            'status_disable'=>function($url,$model)
            {
                return Html::a('禁用',$url,['title' => '禁用', 'class' => 'disable', 'data-toggle' => false]);
            },
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

    'toolbar'=> [
        [
            'content'=>  Html::button('全选',['class' => 'btn btn-default all_select','value'=>'2']).'&nbsp;&nbsp;'.Html::button('批量通过',['class' => 'btn btn-default select_pass','value'=>'1']).'&nbsp;&nbsp;'.Html::button('批量禁用',['class' => 'btn btn-default select_refuse','value'=>'0'])
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
    'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]],
    'panel' => [
        'type' => GridView::TYPE_INFO
    ],

]);

\yii\bootstrap\Modal::begin([
        'id' => 'multi-modal',
        'clientOptions' => false,
        'header' => Html::button('确定',['class' => 'btn btn-default','id'=>'set_client_id']).' '.Html::button('取消',['aria-hidden'=>'true', 'class' => 'btn btn-default','data-dismiss'=>'modal']),
        'size'=>\yii\bootstrap\Modal::SIZE_SMALL,
    ]
);

echo Html::beginTag('div',['class'=>'mulitremark']);
echo Html::label('对方蜜播账号:','input_remark',['class'=>'check-item']);
echo Html::input('text','client_id_value',null,['class'=>'form-control my-input','id'=>'client_id_value']);
echo Html::endTag('div');
\yii\bootstrap\Modal::end();
echo Html::hiddenInput('IsSubmit','0',['id'=>'IsSubmit']);

$js='
$("#goods_list-pjax").on("click",".unbindWeCat",function(){
    var unbind_type = $(this).attr("data-type");
    var client_id = $(this).attr("data-client-id");
    var url = "/client/unbindwecat";
    var datas = "unbind_type="+unbind_type+"&client_id="+client_id;
    var d5 = artDialog({
            title: "提示",
            content: "确定解除绑定吗？",
            okValue: "确 定",
            ok: function () {
                $.ajax({
                    type: "POST",
                    url: url,
                    data: datas,
                    success: function(data)
                        {
                           data = $.parseJSON(data);
                            if(data.code == "0")
                            {

                                window.location.reload();
                            }
                            else
                            {
                                alert("绑定失败：" + data.message);
                                window.location.reload()
                            }
                        },
                    error: function (XMLHttpRequest, textStatus, errorThrown)
                        {
                            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                         }
                    });
            },
    });

        d5.show();


})
var curUrl = null;
$(".content-wrapper").on("click",".balance-modify",function(){
    curUrl = $(this).attr("data-url");
    $("#multi-modal").modal("show");
    return false;
});

$(".content-wrapper").on("click",".btn_phone",function(){
    $(this).html($(this).data("phone"))
});
$("#set_client_id").on("click",function(){
    isSub = $("#IsSubmit").val();
    if(isSub == "1")
    {
        return;
    }

    client_no = $("#client_id_value").val();
    client_no = client_no.replace(/(^\s*)|(\s*$)/g, "");
    if(client_no.length == 0 || isNaN(client_no))
    {
        artDialog.tips("账号必须是数字");
        return;
    }
    $("#IsSubmit").val("1");
    dataStr = "client_no="+client_no;
            $.ajax({
        type: "POST",
        url: curUrl,
        data:dataStr,
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                     $("#goods_list").yiiGridView("applyFilter");
                     $("#multi-modal").modal("hide");
                 }
                 else
                 {
                     alert("更换异常:" + data.msg);
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


$(document).on("click",".all_select",function(){
    if($(this).val()%2==0){
         $(".select_check").prop("checked","checked");
         $(this).val(1).text("取消");
    }else{
       $(this).val(2).text("全选");
        $(".select_check").removeAttr("checked");
    }

});

//批量审核通过
$(document).on("click",".select_pass",function(){

    length = $(".select_check:checked").length;
        if(length <= 0){
            var d2 = artDialog({
                title: "提示",
                content: "请至少选择一项",
                okValue: "确 定",
                ok: function () {
                    artDialog.close();

                },
            });

            d2.show();
            return;
        }
    var flag = false;
    var d3 = artDialog({
            title: "提示",
            content: "确定通过吗？",
            okValue: "确 定",
            ok: function () {
                d3.close();
                var record_ids = new Array();
                $(".select_check:checked").each(function(e){
                    record_ids[e] = $(this).val();
                });
                    var str_ids = record_ids.join("-");
                    var datas = "data="+str_ids+"&check_res=1"
                is_ajax("/client/checkbatch",datas);
            },
            cancelValue: "取消",
			cancel: function () {
				d3.close();
			}
        });

        d3.show();

});

//批量审核拒绝
$(document).on("click",".select_refuse",function(){
    length = $(".select_check:checked").length;
        if(length <= 0){
            var d2 = artDialog({
                title: "提示",
                content: "请至少选择一项",
                okValue: "确 定",
                ok: function () {
                    artDialog.close();

                },
            });

            d2.show();
            return;
        }


    var d4 = artDialog({
            title: "提示",
            content: "确定禁用吗？",
            okValue: "确 定",
            ok: function () {
                d4.close();
                var record_ids = new Array();
                $(".select_check:checked").each(function(e){
                    record_ids[e] = $(this).val();
                });
                    var str_ids = record_ids.join("-");
                    var datas = "data="+str_ids+"&check_res=2";
                    //alert(datas);
                    is_ajax("/client/checkbatch",datas);
            },
            cancelValue: "取消",
			cancel: function () {
				d4.close();
			}
    });

    d4.show();

});


function is_ajax (url,datas){
    $.ajax({
        type: "POST",
        url: url,
        data: datas,
        dataType: "json",
        async:false ,
        success: function(data)
            {
                //alert(data);
                $("#has_submit").val("0");
                if(data.code == "0")
                {
                    var d = artDialog({
                        title: "提示",
                        content: "数据提交成功",
                        okValue: "确 定",
                        ok: function () {
                            artDialog.close();
                            window.location.reload()
                        },
                    });
                    d.show();
                     $("#contact-modal").modal("hide");
                     $("#user-manage-list").yiiGridView("applyFilter");
                 }
                 else
                 {
                     var d = artDialog({
                        title: "提示",
                        content: "设置失败：" + data.msg,
                        okValue: "确 定",
                        ok: function () {
                            artDialog.close();
                            window.location.reload()
                        },
                    });
                    d.show();
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                if(XMLHttpRequest.status == 200)
                {
//                    alert("禁用的用户中含有超管，超管不能被禁用！~~~~o(≥v≤)o~");
                    $("#has_submit").val("0");
                }
                else
                {
                    alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                    $("#has_submit").val("0");
                }
             }
        });
}


$(function(){
        $("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
    });
';
$this->registerJs($js,\yii\web\View::POS_END);