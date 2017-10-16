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
        color: #00a7d0;
        border:1px solid #00a7d0;
        padding: 3px 5px;
    }
</style>
<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    [
        'attribute'=>'title',
        'vAlign'=>'middle',
        'filter'=>false
    ],
    [
        'width'=>'280px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{set_key}{msg}{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action) {
                case 'delete':
                    $url = '/laterimage/delete_params?id='.strval($model->id);
                    break;
                case 'msg':
                    $url = '/laterimage/index_msg?id='.strval($model->id);
                    break;
                case 'set_key':
                    $url = '/laterimage/get_key?id='.strval($model->id);
                    break;
            }
            return $url;
        },
        'buttons'=>[
            'delete'=>function($url, $model) {
                return Html::a('删除', $url, ['class'=>'delete back-a','data-toggle'=>false,'data-pjax'=>'0']);
            },
            'msg'=>function($url, $model){
                return Html::a('设置图片', $url, ['class'=>'back-a','style'=>'margin-right:10px']);
            },
            'set_key'=>function($url, $model) {
                return Html::a('设置关键字', $url, ['class'=>'back-a', 'style'=>'margin-right:10px','data-toggle'=> 'modal', 'data-target'=>'#contact-modal']);
            }

        ],
    ]
];

echo GridView::widget([
    'id'=>'later_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('新增配置',['type'=>'button','id'=>'create-day' ,'class'=>'btn btn-success',  'onclick' => 'location="'.Yii::$app->urlManager->createUrl(['laterimage/create_params']).'"; return false;']),
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
        'size'=>\yii\bootstrap\Modal::SIZE_DEFAULT,
    ]
);
$js='
$("#later_list-pjax").on("click",".delete",function(){
 if (!confirm("确定要删除该记录吗？")) {
        return false;
    }
    $url = $(this).attr("href");
            $.ajax({
        type: "POST",
        url: $url,
        data: "",
        success: function(data) {
           data = $.parseJSON(data);
           if (data.code == "0") {
                $("#later_list").yiiGridView("applyFilter");
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