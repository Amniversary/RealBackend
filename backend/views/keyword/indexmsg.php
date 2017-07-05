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
if(!$is_verify){
    echo \yii\bootstrap\Alert::widget([
        'body'=>'公众号未认证，无法进行相应操作！',
        'options'=>[
            'class'=>'alert-warning',
        ]
    ]);
}

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'app_id',
        'vAlign'=>'middle',
        'value'=>function($model){
            return $model::getKeyAppId($model->app_id);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'key_id',
        'vAlign'=>'middle',
        'value'=>function($model){
            return \common\models\Keywords::getKeyName($model->key_id);
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
        'attribute'=>'title',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'description',
        'vAlign'=>'middle',
        'value'=>function($model){
            $len = strlen($model->description);
            if($len > 10){
                return mb_substr($model->description,0,15) . '....';
            }else{
                return $model->description;
            }
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'content',
        'vAlign'=>'middle',
        'format'=>'html',
        'filter'=>false,
    ],
    [
        'attribute'=>'event_id',
        'vAlign'=>'middle',
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
        'template'=>'{update}&nbsp;&nbsp;&nbsp;{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'200px',
        'urlCreator'=> function($action, $model , $key , $index){
            $url = '';
            switch ($action){
                case 'update':
                    $url = '/keyword/updatemsg?record_id='.strval($model->record_id);
                    break;
                case 'delete':
                    $url = '/keyword/deletemsg?record_id='.strval($model->record_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'update'=>function($url, $model){
                return Html::a('修改', $url,['class'=>'back-a','style'=>'margin-right:10px']);
            },
            'delete'=>function($url, $model){
                return Html::a('删除',$url,['class'=>'delete back-a','data-toggle'=>false]);
            }
        ],
    ],

];

echo GridView::widget([
    'id'=>'keywordMsg',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('添加回复消息',['id'=>'cry-msg','type'=>'button','title'=>'添加回复', 'class'=>'btn btn-success']),
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

echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

$js='
$("#cry-msg").on("click",function(){
    $url = "http://"+ window.location.host + "/keyword/check";
    $.ajax({
        type:"POST",
        url:$url,
        data: "",
        success: function(data){
            data = $.parseJSON(data);
            if(data.code == "0"){
                location="'.\Yii::$app->urlManager->createUrl('keyword/createmsg').'";
                return false;
            }else{
                alert(data.msg);
                return false;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
        }
    })
});
$("#keywordMsg-pjax").on("click",".delete",function(){
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
                    $("#keywordMsg").yiiGridView("applyFilter");
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