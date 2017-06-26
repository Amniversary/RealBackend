<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:48
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'record_id',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'ticket_num',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'bean_num',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'status',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return ($model->status == '1')?'已受理':(($model->status == '2')?'已审核':'拒绝');
        }
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'check_time',
        'vAlign'=>'middle',
        'value' => function($model)
        {
            return ($model->status == '1')?'未审核':(($model->status == '2')?$model->check_time:'拒绝');
        }
    ],
    [
        'width'=>'120px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/goods/update?goods_id='.strval($model->record_id);
                    break;
                case 'delete':
                    $url = '/goods/delete?goods_id='.strval($model->record_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'update' => function ($url, $model, $key)
            {
                return Html::a('编辑',$url,[ 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
            },
            'delete' =>function ($url, $model,$key)
            {
                return Html::a('删除',$url,[ 'data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px','id'=>'goods_delete','data-confirm'=>'确定要删除该记录吗？','data-method'=>'post']);
            },
        ],
    ],
];

echo GridView::widget([
    'id'=>'goods_list',
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
            'content'=> Html::button('新增商品',['type'=>'button','title'=>'新增商品', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('goods/create').'";return false;']),
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
$("#goods_delete").on("click",function(){
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
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
             }
        });
});
';
$this->registerJs($js,\yii\web\View::POS_END);