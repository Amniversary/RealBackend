<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/27
 * Time: 10:24
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label'=>'蜜播 ID',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'主播昵称',
    ],
    [
        'attribute'=>'activity_id',
        'vAlign'=>'middle',
        'label'=>'活动名称',
        'value'=>function($model)
        {
            return \familyend\business\ActivityUtil::GetActivityName($model['activity_id']);
        },
        'filter'=>\familyend\business\ActivityUtil::GetActivityType(),
    ],
    [
        'width'=>'300px',
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
                    $url = '/activitypeople/delete?record_id='.strval($model['record_id']);
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
    'id'=>'activity_list',
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
            'content'=> Html::button('新增预设主播',['type'=>'button','title'=>'新增预设主播', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('activitypeople/create').'";return false;']),
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

echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

$js='
$("#activity_list-pjax").on("click",".delete",function(){
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
                    $("#activity_list").yiiGridView("applyFilter");
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