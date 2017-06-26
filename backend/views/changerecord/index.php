<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 17:11
 */


\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;
use \yii\bootstrap\Tabs;


echo Tabs::widget([
    'options'=>['class'=>'ctr-head'],
    'items' => [
        [
            'label' => '未审核',
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['changerecord/index']),// $this->render('indexundo'),
            'active' => ($data_type === 'noexamine'? true: false),
            'options' => ['id' => 'my_comment_undo'],
        ],
        [
            'label' => '已审核',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['changerecord/indexexamine']),//$this->render('indexhis'),
            'headerOptions' => [],
            'options' => ['id' => 'my_comment_his'],
            'active' => ($data_type === 'examine'? true: false)
        ],
    ],
]);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{select_all}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'header' => '选择',
        'buttons'=>[
            'select_all' => function ($url, $model, $key)
            {
                return Html::checkbox(false,'',['class'=>'select_check','value'=>strval($model['record_id'])]);
            },
        ],
    ],
    [
        'attribute'=>'user_id',
        'vAlign'=>'middle',
        'label' =>'用户id',
        'width'=>'150px',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label' =>'密播id',
        'width'=>'150px',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label' =>'密播昵称',
        'width'=>'100px',
    ],
    [
        'attribute'=>'user_name',
        'vAlign'=>'middle',
        'label'=>'用户姓名',
        'width'=>'100px',
    ],
    [
        'attribute'=>'gift_name',
        'vAlign'=>'middle',
        'label'=>'兑换的商品',
        'width'=>'150px',
    ],
    [
        'attribute'=>'change_time',
        'vAlign'=>'middle',
        'label'=>'兑换的时间',
        'width'=>'250px',
    ],
    [
        'attribute'=>'change_state',
        'vAlign'=>'middle',
        'label'=>'发货的状态',
        'width'=>'150px',
        'value'=>function($model)
        {
            return (($model['change_state'] == 1)? '已发货':'未发货');
        },
//        'filter'=>['1'=>'已发货','0'=>'未发货'],
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'address',
        'vAlign'=>'middle',
        'label'=>'用户的地址',
        'width'=>'500',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'address',
                'formOptions'=>['action'=>'/changerecord/set_address?record_id='.strval($model['record_id'])],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                'value'=>$model['address'],
            ];
        },
        'refreshGrid'=>true,
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
                    $url = '/changerecord/detail?record_id='.strval($model['record_id']);

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

    'toolbar'=> [
        [
            'content'=>  Html::button('全选',['class' => 'btn btn-default all_select','value'=>'2']).'&nbsp;&nbsp;'.Html::button('批量通过',['class' => 'btn btn-default select_pass','value'=>'1']).'&nbsp;&nbsp;'.Html::button('批量拒绝',['class' => 'btn btn-default select_refuse','value'=>'0'])
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

echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

$js='

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
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                $("#has_submit").val("0");
             }
        });
}




$(document).on("click",".all_select",function(){
    if($(this).val()%2==0){
         $(".select_check").prop("checked","checked");
         $(this).val(1).text("取消");
    }else{
       $(this).val(2).text("全选");
        $(".select_check").removeAttr("checked");
    }

});

var dialog = null;

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
            content: "确定审核通过吗？",
            okValue: "确 定",
            ok: function () {
                d3.close();
                var record_ids = new Array();
                $(".select_check:checked").each(function(e){
                    record_ids[e] = $(this).val();
                });
                    var str_ids = record_ids.join("-");
                    var datas = "record_id="+str_ids+"&change_state=1"
                    //alert(datas);
                    is_ajax("/changerecord/all_check",datas);
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
            content: "确定拒绝吗？",
            okValue: "确 定",
            ok: function () {
                d4.close();
                var record_ids = new Array();
                $(".select_check:checked").each(function(e){
                    record_ids[e] = $(this).val();
                });
                    var str_ids = record_ids.join("-");
                    var datas = "record_id="+str_ids+"&change_state=2";
                    is_ajax("/changerecord/all_check",datas);
            },
            cancelValue: "取消",
			cancel: function () {
				d4.close();
			}
    });

    d4.show();

});

$(function(){
        $("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
    });
';
$this->registerJs($js,\yii\web\View::POS_END);