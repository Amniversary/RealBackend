<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'gift_id',
        'vAlign'=>'middle',
        'label'=>'礼物 ID',
        'width'=>'300px',
    ],
    [
        'attribute'=>'gift_name',
        'vAlign'=>'middle',
        'label'=>'礼物名称',
        'width'=>'350px',
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'score',
        'vAlign'=>'middle',
        'label'=>'礼物积分',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'score',
                'formOptions'=>['action'=>'/scoregift/set_score?record_id='.strval($model['record_id'])],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                'value'=>$model['score'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'pic',
        'vAlign'=>'middle',
        'label'=>'礼物图片',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = $model['pic'];
            return Html::img($url,['class'=>'pic','style'=>'width:50px']);
        }
    ],

    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'delete':
                    $url = '/scoregift/score_delete?record_id='.strval($model['record_id']);
                    break;
            }
            return $url;
        },
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'delete' =>function ($url, $model,$key)
            {
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-pjax'=>'0','style'=>'margin-left:10px']);
            },
        ],
    ],
];

echo GridView::widget([
    'id'=>'gift_score_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[
        [
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar'=> [
        [
            'content'=> Html::button('新增礼物积分',['type'=>'button','title'=>'新增礼物积分', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('scoregift/score_create').'";return false;']),
        ],
        '{export}',
        '{toggleData}',
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']

    ],
    'pjax' => true,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]],
    'panel' => [
        'type' => GridView::TYPE_INFO
    ],

]);

$js='
$("#gift_score_list-pjax").on("click",".delete",function(){
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
                    $("#gift_score_list").yiiGridView("applyFilter");
                 }
                 else
                 {
                     alert("删除失败：" + data.msg);
                     //window.location.reload()
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