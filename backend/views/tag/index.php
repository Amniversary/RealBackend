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

$gridColumns = [
    [
        'attribute'=>'id',
        'vAlign'=>'middle',
        'label'=> '#',
        'filter'=>false
    ],
    [
        'attribute'=>'tag_name',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{get_auth}{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'250px',
        'urlCreator'=>function($action, $model, $key, $index){
            $url = '';
            switch ($action) {
                case 'delete':
                    $url = '/tag/delete?id='.intval($model->id);
                    break;
                case 'get_auth';
                    $url = '/tag/get_auth?id='.intval($model->id);
                    break;
            }
            return $url;
        },
        'viewOptions'=>['title'=>'查看', 'data-toggle'=>'tooltip'],
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'delete'=>function($url, $model){
                return Html::a('删除',$url,['class'=>'delete back-a','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-method'=>'post', 'data-pjax'=>'1']);
            },
            'get_auth'=>function($url, $model){
                return Html::a('设置公众号', $url, ['class'=>'back-a', 'data-toggle'=>'modal', 'style'=>'margin-right:3%', 'data-target'=> '#contact-modal']);
            }
        ],
    ]


];

echo GridView::widget([
    'id'=>'tag_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('新增标签',['type'=>'button','class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('tag/create').'"; return false']),
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


echo yii\bootstrap\Modal::widget([
   'id'=>'contact-modal',
    'clientOptions' => false,
    'size' => yii\bootstrap\Modal::SIZE_LARGE
]);
$js='
$("#tag_list-pjax").on("click",".delete",function(){
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
                    $("#tag_list").yiiGridView("applyFilter");
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