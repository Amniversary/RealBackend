<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 16:51
 */

\common\assets\ArtDialogAsset::register($this);
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:38
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;


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
                return Html::checkbox(false,'',['class'=>'select_check','value'=>strval($model['dynamic_id'])]);
            },
        ],
    ],
    [
        'label'=>'动态ID',
        'vAlign'=>'middle',
        'attribute'=>'dynamic_id',
        'width'=>'80px'
    ],
    [
        'label'=>'密播ID',
        'vAlign'=>'middle',
        'attribute'=>'client_no',
        'width'=>'150px'
    ],
    [
        'label'=>'客户昵称',
        'vAlign'=>'middle',
        'attribute'=>'nick_name',
    ],
    [
        'label'=>'动态内容',
        'vAlign'=>'middle',
        'attribute'=>'content',
    ],
    [
        'label'=>'用户图片',
        'vAlign'=>'middle',
        'format'=>'html',
        'attribute'=>'pic',
        'value'=>function($model)
        {
            return Html::img($model['pic'],['class'=>'user-pic','style'=>'width:100px;height:100px','title'=>$model['pic']]);

        },
        'filter'=>false,
    ],
    [
        'label'=>'创建时间',
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'width'=>'200px',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'label'=>'动态类型',
        'attribute'=>'dynamic_type',
        'vAlign'=>'middle',
        'format'=>'html',
        'width'=>'80px',
        'value'=>function($model)
        {
            switch($model['dynamic_type'])
            {
                case 1:
                    $type = '普通动态';
                    break;
                case 2:
                    $type = '红包动态';
                    break;
            };
            return $type;

        },
        'filter'=>['1'=>'普通动态','2'=>'红包动态']
    ],
    [
        'label'=>'点赞数',
        'vAlign'=>'middle',
        'attribute'=>'click_num',
        'width'=>'80px'
    ],
    [
        'label'=>'评论数',
        'vAlign'=>'middle',
        'attribute'=>'comment_num',
        'width'=>'80px'
    ],
    [
        'label'=>'打赏数',
        'vAlign'=>'middle',
        'attribute'=>'check_num',
        'width'=>'80px'
    ],
    [
        'label'=>'打赏金额',
        'vAlign'=>'middle',
        'attribute'=>'red_pic_money',
        'width'=>'80px'
    ],
    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{view_larger}&nbsp;&nbsp;{delete}&nbsp;&nbsp;{view_all}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'delete':
                    $url = '/clientalbum/delete?dynamic_id='.strval($model['dynamic_id']).'&type=index';
                    break;
                case 'view_all':
                    $url = '/clientalbum/view_all?user_id='.strval($model['user_id']);
                    break;
            }
            return $url;
        },
        'buttons'=>[
            'delete'=>function($url,$model)
            {
                return Html::a('删除', $url, ['title' => '删除', 'class' => 'delete', 'data-toggle' => false, 'data-confirm' => '请勿删除非违规图片，一经删除，无法恢复，请确认是否删除选中照片']);
            },
            'view_larger'=>function($url,$model)
            {
                return Html::a('查看大图', $url, ['title' => '查看大图', 'class' => 'view-larger', 'data-toggle' => false]);
            },
            'view_all'=>function($url,$model)
            {
                return Html::a('查看用户相册', $url, ['title' => '查看用户相册', 'class' => 'view_all', 'data-toggle' => false,'target' => '_blank']);
            },
        ],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'id'=>'user-manage-list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:500px;'], // only set when $responsive = false
    'beforeHeader'=>[
        [

            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar' =>  [
        [
            'content'=>  Html::button('全选',['class' => 'btn btn-default all_select','value'=>'2']).'&nbsp;&nbsp;'.Html::button('批量删除',['class' => 'btn btn-default select_delete','value'=>'1'])
        ],
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
        'type' => GridView::TYPE_PRIMARY
    ],
]);

$js='
$(document).on("click",".all_select",function(){
    if($(this).val()%2==0){
         $(".select_check").prop("checked","checked");
         $(this).val(1).text("取消");
    }else{
       $(this).val(2).text("全选");
        $(".select_check").removeAttr("checked");
    }

});

$("body").on("click",".view-larger",function(){
    var img = $(this).parent().parent().find(".user-pic").attr("title");
    if(img != ""){
        art.dialog({
            content: "<img style=\"width:640px;max-height: 640px;\" src=\" "+ img + " \">",
            title:"用户图片",
            cancelVal: "关闭",
            cancel: true //为true等价于function(){}
        });
    }
});

var dialog = null;

//批量删除
$(document).on("click",".select_delete",function(){

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
            content: "确定批量删除吗？",
            okValue: "确 定",
            ok: function () {
                d3.close();
                var record_ids = new Array();
                $(".select_check:checked").each(function(e){
                    record_ids[e] = $(this).val();
                });

                    var str_ids = record_ids.join("-");
                    var datas = "data="+str_ids
                    is_ajax("/clientalbum/checkbatch",datas);
            },
            cancelValue: "取消",
			cancel: function () {
				d3.close();
			}
        });

        d3.show();

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
                $("#has_submit").val("0");
               //data = $.parseJSON(data);
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
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                $("#has_submit").val("0");
             }
        });
}

';
$this->registerJs($js,\yii\web\View::POS_END);