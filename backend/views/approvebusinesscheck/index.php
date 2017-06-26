<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:48
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
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['approvebusinesscheck/index']),// $this->render('indexundo'),
            'active' => ($data_type === 'check'? true: false),
            'options' => ['id' => 'my_comment_undo'],
        ],
        [
            'label' => '已审核',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['approvebusinesscheck/indexaudited']),//$this->render('indexhis'),
            'headerOptions' => [],
            'options' => ['id' => 'my_comment_his'],
            'active' => ($data_type === 'audited'? true: false)
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
        'buttons'=>[
            'select_all' => function ($url, $model, $key)
            {
                return Html::checkbox(false,'',['class'=>'select_check','value'=>strval($model['approve_id'])]);
            },
        ],
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label' => '蜜播ID',
    ],
    [
        'attribute'=>'create_user_name',
        'vAlign'=>'middle',
        'label' => '用户名',
    ],
    [
        'attribute'=>'actual_name',
        'vAlign'=>'middle',
        'label' => '真实姓名',
    ],
    [
        'attribute'=>'phone_num',
        'vAlign'=>'middle',
        'label' => '手机号',
    ],
//    [
//        'attribute'=>'id_card',
//        'vAlign'=>'middle',
//        'label' => '身份证号',
//    ],
//    [
//        'attribute'=>'bank_account',
//        'vAlign'=>'middle',
//        'label' => '银行卡号',
//    ],
//    [
//        'attribute'=>'account_name',
//        'vAlign'=>'middle',
//        'label' => '开户名',
//    ],
//    [
//        'attribute'=>'wechat',
//        'vAlign'=>'middle',
//        'label' => '微信',
//    ],
//    [
//        'attribute'=>'qq',
//        'vAlign'=>'middle',
//        'label' => 'QQ',
//    ],
    [
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label' => '状态',
        'value'=>function($model)
        {
            return $model['status'] = ($model['status']==0?'未审核':'已审核');
        },
        'filter'=>['0'=>'未审核','1'=>'已审核'],
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
//    [
//        'attribute'=>'check_time',
//        'vAlign'=>'middle',
//        'label' => '审核时间',
//    ],
    [
        'width'=>'120px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            $date_type = $model['status'] == '0'?'check':'audited';
            switch($action)
            {
                case 'update':
                    $url = '/approvebusinesscheck/detail?date_type='.$date_type.'&approve_id='.strval($model['approve_id']);

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
$js = '
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
                    var d_fail = artDialog({
                        title: "提示",
                        content: "数据提交成功",
                        okValue: "确 定",
                        ok: function () {
                            artDialog.close();
                            window.location.reload();
                        },
                        close:function()
                        {
                            window.location.reload();
                        }
                    });
                    d_fail.show();
                 }
                 else
                 {
                     var d_ok = artDialog({
                        title: "提示",
                        content: "设置失败：" + data.msg,
                        okValue: "确 定",
                        ok: function () {
                            artDialog.close();
                            window.location.reload();
                        },
                        close:function()
                        {
                            window.location.reload();
                        }
                    });
                    d_ok.show();

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
                var approve_ids = new Array();
                $(".select_check:checked").each(function(e){
                    approve_ids[e] = $(this).val();
                });
                    var str_ids = approve_ids.join("-");
                    var datas = "data="+str_ids+"&refused_reason=&check_res=1"
                is_ajax("/approvebusinesscheck/checkbatch",datas);
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

                 var d0 = artDialog({
                    title: "拒绝原因",
                    content: "<textarea style=\"width: 500px;height: 200px;\" id=\"refuse_check\"></textarea>",
                    okValue: "确 定",
                    ok: function () {
                        var refused_reason = $("#refuse_check").val();
                            if(refused_reason.trim()==""){
                                var d5 = artDialog({
                                    title: "提示",
                                    content: "拒绝原因不能为空",
                                    okValue: "确 定",
                                    ok: function () {
                                        d5.close();
                                    },
                                });

                                d5.show();
                                return;
                            }

                            d0.close();

                            var approve_ids = new Array();
                            $(".select_check:checked").each(function(e){
                                approve_ids[e] = $(this).val();
                            });
                                var str_ids = approve_ids.join("-");
                                var datas = "data="+str_ids+"&refused_reason="+refused_reason+"&check_res=0"
                            is_ajax("/approvebusinesscheck/checkbatch",datas);
                    },
                });
                d0.show();

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