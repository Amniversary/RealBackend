<style>
    .user-pic{
        width: 60px;
        height: 60px;
    }
    .alert{
        padding: 10px;
    }
    .content-header{
        position: relative;
        padding: 1px 15px 0 15px;
    }
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
        padding: 6px 12px;
    }
</style>
<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;

echo \yii\bootstrap\Alert::widget([
    'body'=>'未认证公众号，默认回复第一条消息！',
    'options'=>[
        'class'=>'alert-warning',
    ]
]);
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'msg_type',
        'vAlign'=>'middle',
        'width'=>'130px',
        'value'=>function($model){
            return $model->getMsgType($model->msg_type);
        },
        'filter'=>['0'=>'文本消息','1'=>'图文消息','2'=>'图片消息','3'=>'语音消息'],
    ],
    [
        'attribute'=>'content',
        'vAlign'=>'middle',
        'format'=>'html',
        'value'=>function($model){
            $len = strlen($model->content);
            return $len > 20 ? mb_substr($model->content,0,15) . '....' : $model->content;
        },
        'filter'=>false,
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
            return $len > 20 ? mb_substr($model->description,0,15) . '....': $model->description;
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'url',
        'vAlign'=>'middle',
        'width'=>'100px',
        'value'=>function($model){
            $len = strlen($model->url);
            return $len > 20 ? mb_substr($model->url,0,15) . '....' : $model->url;
        }
    ],
    [
        'attribute'=>'picurl',
        'vAlign'=>'middle',
        'width'=>'100px',
        'format'=>'html',
        'value'=>function($model){
            $url = empty($model->picurl) ? '': $model->picurl;
            return empty($url) ? Html::label('') :Html::img($url,['class'=>'user-pic']);
        }
    ],
    [
        'attribute'=>'video',
        'vAlign'=>'middle',
        'width'=>'100px',
        'format'=>'html',
        'content'=>function($model) {
            $url = empty($model->video)? '':"<audio controls='controls' src=$model->video></audio>";
            return $url;
        }
    ],
    [
        'attribute'=>'event_id',
        'vAlign'=>'middle',
        'value'=>function($model){
            return empty($model->event_id) ? '':$model->event_id;
        },
        'width'=>'100px',
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute'=>'order_no',
        'vAlign'=>'middle',
        'width'=>'100px',
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/keyword/order_no?record_id='.strval($model->record_id)],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
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
        'template'=>'{update}{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'250px',
        'urlCreator'=> function($action, $model , $key , $index){
            $url = '';
            switch ($action){
                case 'update':
                    $url = '/sign/batchupdate_msg?record_id='.strval($model->record_id);
                    break;
                case 'delete':
                    $url = '/sign/delete_msg?record_id='.strval($model->record_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'update'=>function($url, $model){
                return Html::a('修改', $url,['class'=>'back-a','style'=>'margin-right:3%']);
            },
            'delete'=>function($url, $model){
                return Html::a('删除',$url,['class'=>'delete back-a','data-toggle'=>false]);
            }
        ],
    ],

];

echo GridView::widget([
    'id'=>'sign_msg_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('返回',['type'=>'button','class'=>'btn btn-success','onclick'=>'location="'.\Yii::$app->urlManager->createUrl('sign/batchindex').'";return false;']).
            Html::button('添加回复消息',['type'=>'button','title'=>'添加回复', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl(['sign/batch_create_msg', 'id'=>$id]).'";return false']),
        ],
        //'{export}',
        //'{toggleData}',
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
$(document).on("click", ".back-a",function(){
    $(".back-a").attr("href", $(this).attr("href") + "&id='.$id.'");
});

$("#sign_msg_list-pjax").on("click",".delete",function(){
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
                    $("#sign_msg_list").yiiGridView("applyFilter");
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