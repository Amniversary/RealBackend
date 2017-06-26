<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19
 * Time: 19:45
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;


$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'client_no',
        'label'=>'密播ID',
    ],
    [
        'attribute'=>'remark2',
        'label' => '创建日期',
        'vAlign'=>'middle',
        'width'=>'350px',
        'filterType'=>'\yii\jui\DatePickerRange',
        'filterWidgetOptions'=>[
            'language'   => 'zh-CN',
            'dateFormat' => 'yyyy-MM-dd',
            'options'    => [
                'class'=>'form-control',
                'style'=>'display:inline-block;width:100px;'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'changeYear'  => true,
            ]
        ]
    ],

    [
        'attribute'=>'remark3',
        'label'=>'操作人',
    ],
    [
        'width'=>'150px',
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
                    $url='/whitelist/delete?id='.$model['client_no'];
                    break;
            }
            return $url;
        },
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'delete' => function ($url, $model, $key) {
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-pjax'=>'1','style'=>'margin-left:10px']);
            },
        ],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'id'=>'goods_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:620px;'], // only set when $responsive = false
    'beforeHeader'=>[
        [
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar' =>  [
        [
            'content'=> Html::button('新增',['type'=>'button','title'=>'新增', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('whitelist/add').'";return false;']),
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
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);


$js='

$("body").on("click",".delete",function(){
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
                    $("#goods_list").yiiGridView("applyFilter");
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


