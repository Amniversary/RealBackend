<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:48
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
\common\assets\ArtDialogAsset::register($this);
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'app_type',
        'vAlign'=>'middle',
        'value' => function($model)
        {
            return ($model->app_type==1?'android':'ios');
        }
    ],
    [
        'attribute'=>'app_id',
        'vAlign'=>'middle',
    ],

    [
        'attribute'=>'app_name',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label' => '禁用',
        'format' =>'raw',
        'value'=>function($model)
        {
            if($model->status == '1'){
                return Html::button('正常',['style'=>'background-color: transparent;border: none;color:#428bca;','class' => 'btn btn-default all_select SetStatus','data-status'=>'0','data-record-id'=>$model->record_id]);
            }else{
                return Html::button('禁用',['style'=>'background-color: transparent;border: none;color:#428bca;','class' => 'btn btn-default all_select SetStatus','data-status'=>'1','data-record-id'=>$model->record_id]);
            }
        },
        'filter'=>['1'=>'正常','0'=>'禁用'],
    ],
    [
        'attribute'=>'forbid_words',
        'vAlign'=>'middle',
        'label' => '禁用提示'
    ],
    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;{add}&nbsp;&nbsp{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/versionmanage/update?record_id='.strval($model->record_id);
                    break;
                case 'add':
                    $url = '/versionmanage/indexson?app_id='.strval($model->app_id);
                    break;
                case 'delete':
                    $url = '/versionmanage/delete?record_id='.strval($model->record_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'buttons'=>[
            'update' => function ($url, $model, $key)
            {
                return Html::a('编辑',$url,['style'=>'margin-left:10px']);
            },
            'add' => function ($url, $model, $key)
            {
                return Html::a('子版本管理',$url,['style'=>'margin-left:10px']);
            },
            'delete' => function ($url, $model, $key)
            {
                return Html::a('删除',$url,['style'=>'margin-left:10px','id'=>'goods_delete', "class"=>"goods_delete",]);
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
            'content'=> Html::button($add_title,['type'=>'button','title'=>$add_title, 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('versionmanage/create').'";return false;']),
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

$js='
$("#goods_list-pjax").on("click",".goods_delete",function(){
    if(!confirm("确定要删除此记录吗？"))
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
                    $("#goods_list").yiiGridView("applyFilter");
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


$("#goods_list-pjax").on("click",".SetStatus",function(){
    var data_status = $(this).attr("data-status");
    var record_id = $(this).attr("data-record-id");
    var url = "/versionmanage/setstatus";
    if(data_status == 0){
        var d0 = artDialog({
                    title: "禁用提示",
                    content: "<textarea style=\"width: 500px;height: 200px;\" id=\"forbid_words\"></textarea>",
                    okValue: "确 定",
                    ok: function () {
                        var forbid_words = $("#forbid_words").val();
                            if(forbid_words.trim()==""){
                            var d = artDialog({
                                content: "禁用提示不能为空",
                                okValue: "确 定",
                                    ok: function () {
                                        d.close();
                                    },
                            });
                            d.show(follow);
                                return;
                            }

                            d0.close();
                        var datas = "data_status=0&record_id="+record_id+"&forbid_words="+forbid_words;
                        do_ajax(url,datas);
                    },
                });
                d0.show();
    }else{
        var datas = "data_status=1&record_id="+record_id+"&forbid_words=";
        do_ajax(url,datas);
    }

})


    function do_ajax(url,datas){
        $.ajax({
            type: "POST",
            url: url,
            data: datas,
            success: function(data)
                {
                   data = $.parseJSON(data);
                    if(data.code == "0")
                    {
                        alert("数据提交成功");
                     $("#goods_list").yiiGridView("applyFilter");
                     }
                     else
                     {
                         alert("设置失败：" + data.message);
                         $("#goods_list").yiiGridView("applyFilter");
                     }
                },
            error: function (XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                }
            });
    }
';
$this->registerJs($js,\yii\web\View::POS_END);