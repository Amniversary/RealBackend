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
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'app_id',
        'vAlign'=>'middle',
        'value'=>function($model){
            return $model->getKeyAppId($model->app_id);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'msg_type',
        'vAlign'=>'middle',
        'value'=>function($model){
            return $model->getMsgType($model->msg_type);
        },
        'filter'=>['0'=>'文本消息','1'=>'图文消息'],
    ],
    [
        'attribute'=>'event_id',
        'vAlign'=>'事件 ID',
    ],
    [
        'attribute'=>'title',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'description',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'content',
        'vAlign'=>'middle',
        'filter'=>false,
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
        'class'=>'kartik\grid\ActionColumn',
        'template'=>'{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'200px',
        'urlCreator'=> function($action, $model , $key , $index){
            $url = '';
            switch ($action){
                case 'delete':
                    $url = '/publiclist/delete?record_id='.strval($model->record_id);
                    break;
            }
            return $url;
        },
        'buttons'=>[
            'delete'=>function($url, $model){
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-pjax'=>'0']);
            }
        ],
    ],

];

echo GridView::widget([
    'id'=>'attention_event',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('添加回复',['type'=>'button','title'=>'添加回复', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('publiclist/createmsg').'";return false;']),
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
$("#attention_event-pjax").on("click",".delete",function(){
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
                    $("#attention_event").yiiGridView("applyFilter");
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