<style>
    .user-pic{
        width: 60px;
        height: 60px;
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
/*if(!$is_verify){
    echo \yii\bootstrap\Alert::widget([
        'body'=>'公众号未认证，无法进行相应操作！',
        'options'=>[
            'class'=>'alert-warning',
        ]
    ]);
}*/
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'msg_type',
        'vAlign'=>'middle',
        'width'=>'100px',
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
            return  $model->content;
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
            return $len > 10 ? mb_substr($model->description,0,15) . '....': $model->description;
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'url',
        'vAlign'=>'middle',
        'width'=>'100px',
        'value'=>function($model){
            $len = strlen($model->url);
            return $len > 10 ? mb_substr($model->url,0,15) . '....' : $model->url;
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
                'formOptions'=>['action'=>'/batchkeyword/order_no?record_id='.strval($model->record_id)],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'width'=>'150px',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'class'=>'kartik\grid\ActionColumn',
        'template'=>'{get_key}{update}{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'290px',
        'urlCreator'=> function($action, $model , $key , $index){
            $url = '';
            switch ($action){
                case 'get_key':
                    $url = '/batchkeyword/getkeylist?record_id='.strval($model->record_id);
                    break;
                case 'update':
                    $url = '/batchkeyword/updatemsg?record_id='.strval($model->record_id);
                    break;
                case 'delete':
                    $url = '/batchkeyword/deletemsg?record_id='.strval($model->record_id);
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
            },
            'get_key'=>function($url, $model) {
                return Html::a('设置关键字', $url,['class'=>'back-a', 'style'=>'margin-right:3%', 'data-toggle'=>'modal','data-target'=>'#contact-modal']);
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
            'content'=> Html::button('返回',['type'=>'button','class'=>'btn btn-success','onclick'=>'location="'.\Yii::$app->urlManager->createUrl('batchkeyword/index').'";return false;']).Html::button('添加回复消息',['id'=>'cry-msg','type'=>'button','title'=>'添加回复', 'class'=>'btn btn-success']),
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
$(document).on("click","#cry-msg",function(){
    location="'.\Yii::$app->urlManager->createUrl(['batchkeyword/createmsg','key_id'=>$key_id]).'";
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