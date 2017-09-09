<style>
    .page-read {
        color: red;
    }
    .alert{
        padding: 10px;
    }
    .content-header {
        position: relative;
        padding: 1px 15px 0 15px;
    }
    .btn_check_all
    {
        width: 25px;
        height: 25px;
        background-color: #fff;
        color: #008d4c;
        border: 1px solid;
        border-radius: 3px;
        vertical-align: middle;
        text-align: center;
        /*font-weight: bold;*/
    }
</style>
<?php
use kartik\grid\GridView;
use yii\bootstrap\Html;

/**
 * @var $model common\models\ArticleOrder
 * @var $searchModel \backend\models\ArticleOrderSearch
 */

$gridColumns = [
    [
        'label' => '#',
        'attribute' => 'id',
        'vAlign' => 'middle',
        'width' => '70px',
    ],
    [
        'attribute' => 'app_id',
        'vAlign' => 'middle',
        'width' => '100px',
        'value' => function ($model) {
            return \common\models\AttentionEvent::getKeyAppId($model->app_id);
        },
        'filter' => false
    ],
    [
        'class'=>'kartik\grid\ActionColumn',
        'template'=>'{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'200px',
        'urlCreator'=>function($action, $model, $key, $index){
            $url = '';
            switch ($action){
                case 'delete':
                    $url = '/article/delete_order?id='.strval($model->id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'delete'=>function($url, $model){
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete back-a','data-toggle'=>false,'data-pjax'=>'0']);
            }
        ],
    ]
];

echo GridView::widget([
    'id' => 'article_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto;height:750px;font-size:14px;'],
    'beforeHeader' => [['options' => ['class' => 'skip-export']]],
    'toolbar' => [
        [
            'content'=>Html::a('添加公众号', Yii::$app->urlManager->createUrl('article/create') ,['type' => 'button', 'class'=> 'btn btn-primary', 'data-toggle'=> 'modal', 'data-target'=>'#contact-modal']),
        ],
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']
    ],
    'pjax'=>true,
    'bordered' => true,
    'striped' => true,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'panel' => ['type' => GridView::TYPE_INFO],
]);

echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

$js='
$("#article_list-pjax").on("click",".delete",function(){
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
                    $("#article_list").yiiGridView("applyFilter");
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
