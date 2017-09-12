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
 *  @var $model common\models\CustomerStatistics
 */

$gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'],
    [
        'attribute'=>'app_id',
        'vAlign'=>'middle',
        'filter'=>false,
        'value' => function($model) {
            return \common\models\AttentionEvent::getKeyAppId($model->app_id);
        }
    ],
    [
        'attribute'=>'user_count',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'user_num',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'filter'=>false,
        'value'=>function($model) {
            return date('Y-m-d H:i:s', $model->create_time);
        }
    ],
];

echo GridView::widget([
    'id'=>'status_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('返回', ['type'=>'button', 'class'=>'btn btn-primary', 'onclick' => 'location="'.Yii::$app->urlManager->createUrl('template/batch_customer').'"; return false;']),
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
$("#status_list-pjax").on("click",".delete",function(){
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
                $("#status_list").yiiGridView("applyFilter");
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