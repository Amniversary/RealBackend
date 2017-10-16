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
        padding: 6px 12px;
    }
</style>
<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;
/**
 *  @var $model common\models\Keywords
 *  @var $is_verify
 */
$gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'],
    [
        'attribute'=>'keyword',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'rule',
        'vAlign'=>'middle',
        'value'=>function($model){
            return  ($model->rule == 1 ? '精准匹配' : '模糊匹配');
        },
        'filter'=>false
    ],
    [
        'class'=>'kartik\grid\ActionColumn',
        'template'=>'{get_auth}{update}{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'250px',
        'urlCreator'=>function($action, $model, $key, $index){
            $url = '';
            switch ($action){
                case 'update':
                    $url = '/laterimage/update?key_id='.strval($model->key_id);
                    break;
                case 'delete':
                    $url = '/sign/delete?key_id='.strval($model->key_id);
                    break;
                case 'get_auth':
                    $url = '/sign/get_auth?key_id='.strval($model->key_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'update'=>function($url, $model){
                return Html::a('修改', $url,['title'=>'修改信息','style'=>'margin-right:10px','class'=>'back-a']);
            },
            'delete'=>function($url, $model){
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete back-a','data-toggle'=>false,'data-pjax'=>'0']);
            },
            'get_auth'=>function($url, $model) {
                return Html::a('设置公众号', $url, ['class'=>'back-a', 'style'=>'margin-right:10px','data-toggle'=>'modal','data-target'=>'#contact-modal']);
            }
        ],
    ]


];

echo GridView::widget([
    'id'=>'keyword_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('添加关键词',['type'=>'button', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('laterimage/create').'";return false;']),
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
$("#keyword_list-pjax").on("click",".delete",function(){
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
                    $("#keyword_list").yiiGridView("applyFilter");
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