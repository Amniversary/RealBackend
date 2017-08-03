<style>
    .back-a{
    display: inline-block;
    font-size: 14px;
        border-radius: 3px;
        color: #00a7d0;
        border:1px solid #00a7d0;
        padding: 3px 5px;
    }
    .back-btn{
        display: inline-block;
        font-size: 14px;
        margin-bottom: 0px;
        border-radius: 3px;
        color: #00a65a;
        border:1px solid #00a65a;
        padding: 3px 3px;
    }
</style>
<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;
common\assets\ArtDialogAsset::register($this);
/**
 *  @var $model common\models\Template
 *  @var $is_verify
 */
if(!$is_verify){
    echo \yii\bootstrap\Alert::widget([
        'body'=>'公众号未认证，无法进行相应操作！',
        'options'=>[
            'class'=>'alert-warning',
        ]
    ]);
}

$gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'],
    [
        'attribute'=>'title',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'primary_industry',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'deputy_industry',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{send}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'250px',
        'urlCreator'=>function($action, $model, $key, $index){
            $url = '';
            switch ($action){
                case 'send':
                    $url = '/template/get_template?id='.strval($model->id);
                    break;
            }
            return $url;
        },
        'viewOptions'=>['title'=>'查看', 'data-toggle'=>'tooltip'],
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'send'=>function($url, $model){
                return Html::a('配置模板消息', $url,['style'=>'margin-right:10px','class'=>'back-a']);
            }
        ],
    ]
];

echo GridView::widget([
    'id'=>'template_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=>
                Html::button('加载模板', ['id'=>'reload_template','type'=>'button', 'class'=>'btn btn-primary'])
        ],
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']
    ],
    'pjax' => true,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    //'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]],
    'panel' => [
        'type' => GridView::TYPE_INFO
    ],
]);

$js='
artDialog.tips = function(content, time) {
       return artDialog({
            id:"Tips",
            icon:"succeed",
            title:false,
            fixed:true,
            lock:true
       })
       .content("<div style=\"padding: 0 1em;\">" + content + "</div>")
       .time(time || 1);
}
$(document).on("click","#reload_template", function(){
        var dialog = art.dialog({
            title: "获取模板中 ...",
            fixed:true,
            lock:true,
        })
        $.ajax({
            url:"/template/reload_template",
            type:"POST",
            data:"",
            success:function(data) {
                data=$.parseJSON(data);
                if(data.code == 0) {
                    artDialog.tips("模板加载完成!");
                    $("#template_list").yiiGridView("applyFilter");
                }else{
                    art.dialog.alert(data.msg);
                }
                if(dialog != null)
                     dialog.close();
            },
            error:function(XMLHttpRequest, textStatus, errorThrown) {
                artDialog.tips("服务器繁忙，请稍后再试，状态：" + XMLHttpRequest.status);
                if(dialog != null) dialog.close();
            }
        })
});
$("#template_list-pjax").on("click",".delete",function(){
if(!confirm("确定要删除该记录吗？"))
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
                    $("#template_list").yiiGridView("applyFilter");
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
        return false;
});
';
$this->registerJs($js,\yii\web\View::POS_END);