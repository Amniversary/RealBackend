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

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'pic_url',
        'vAlign'=>'middle',
        'width'=>'100px',
        'format'=>'html',
        'value'=>function($model){
            $url = empty($model->pic_url) ? '': $model->pic_url;
            return empty($url) ? Html::label('') :Html::img($url,['class'=>'user-pic']);
        },
        'filter'=>false
    ],
    [
        'class'=>'kartik\grid\ActionColumn',
        'template'=>'{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'50px',
        'urlCreator'=> function($action, $model , $key , $index){
            $url = '';
            switch ($action){
                case 'delete':
                    $url = '/laterimage/delete_msg?record_id='.strval($model->id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
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
            'content'=> Html::button('返回',['type'=>'button','class'=>'btn btn-success','onclick'=>'location="'.\Yii::$app->urlManager->createUrl('laterimage/indexparams').'";return false;']).
            Html::button('添加图片',['type'=>'button', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl(['laterimage/create_msg', 'id'=>$id]).'";return false']),
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
$js='
$(document).on("click", ".back-a",function(){
    $(".back-a").attr("href", $(this).attr("href") + "&id='.$id.'");
});

$("#sign_msg_list-pjax").on("click",".delete",function(){
if(!confirm("确定要删除该记录吗？")) {
        return false;
    }
    $url = $(this).attr("href");
            $.ajax({
        type: "POST",
        url: $url,
        data: "",
        success: function(data) {
            data = $.parseJSON(data);
            if(data.code == "0") {
                $("#sign_msg_list").yiiGridView("applyFilter");
            } else {
                 alert("删除失败：" + data.msg);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
        }
    });
    return false;
});

';
$this->registerJs($js,\yii\web\View::POS_END);