<style>
    .back-a{
        display: inline-block;
        font-size: 14px;
        border-radius: 3px;
        color: #00a65a;
        border:1px solid #00a65a;
        padding: 6px 12px;
    }
    .back-btn{
        display: inline-block;
        font-size: 14px;
        margin-bottom: 0px;
        border-radius: 3px;
        color: #00a65a;
        border:1px solid #00a65a;
        padding: 6px 12px;
    }
</style>
<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'],
    [
        'attribute'=>'name',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'type',
        'vAlign'=>'middle',
        'value'=>function($model){
            $rst = '';
            switch ($model->type){
                case 'click': $rst = '点击事件';break;
                case 'view': $rst = '跳转链接';break;
            }
            return empty($model->type) ? '': $rst;
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'url',
        'vAlign'=>'middle',
        'value'=>function($model){
            return empty($model->url) ? '':$model->url;
        },
        'filter'=>false
    ],
    /*[
        'class'=>'kartik\grid\ActionColumn',
        'template'=>'{son}&nbsp;&nbsp;&nbsp;',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'200px',
        'urlCreator'=>function($action, $model, $key, $index){
            $url = '';
            switch ($action){
                case 'son':
                    $url = '/keyword/update?key_id='.strval($model->menu_id);
                    break;
                case 'delete':
                    $url = '/keyword/delete?key_id='.strval($model->menu_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'son'=>function($url, $model){
                if($model->is_list == 1){
                    return Html::a('二级菜单管理', $url,['title'=>'修改信息','class'=>'back-a']);
                }
                return '';
            },
            'delete'=>function($url, $model){
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete back-a','data-toggle'=>false,'data-pjax'=>'0']);
            }
        ],
    ]*/


];

echo GridView::widget([
    'id'=>'customson_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('返回',['type'=>'button','class'=>'btn btn-success','onclick'=>'location="'.\Yii::$app->urlManager->createUrl('custom/index').'";return false;']).'&nbsp;&nbsp;&nbsp;&nbsp;'.Html::button('添加关键词',['type'=>'button','title'=>'添加关键词', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('keyword/create').'";return false;']),
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
$("#customson_list-pjax").on("click",".delete",function(){
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
                    $("#customson_list").yiiGridView("applyFilter");
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