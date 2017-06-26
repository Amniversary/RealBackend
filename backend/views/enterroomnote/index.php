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
        'attribute'=>'level_no_start',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'level_no_end',
        'vAlign'=>'middle',
        'label' => '结束等级数'
    ],
    [
        'attribute'=>'note_words',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'effect_id',
        'vAlign'=>'middle',
        'label' => '特效等级',
        'value' => function($model)
        {
            switch($model['effect_id']){
                case '1':
                    $model['effect_id'] = "一段特效";
                    break;
                case '2':
                    $model['effect_id'] = "二段特效";
                    break;
                case '3':
                    $model['effect_id'] = "三段特效";
                    break;
                case '4':
                    $model['effect_id'] = "四段特效";
                    break;
                case '5':
                    $model['effect_id'] = "五段特效";
                    break;
                case '6':
                    $model['effect_id'] = "六段特效";
                    break;
                case '7':
                    $model['effect_id'] = "七段特效";
                    break;
                case '8':
                    $model['effect_id'] = "八段特效";
                    break;
                case '9':
                    $model['effect_id'] = "九段特效";
                    break;
                case '10':
                    $model['effect_id'] = "十段特效";
                    break;
            }
            return $model['effect_id'];
        },
    ],
    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;{black}&nbsp;&nbsp;{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/enterroomnote/update?record_id='.strval($model->record_id);
                    break;
                case 'delete':
                    $url = '/enterroomnote/delete?record_id='.strval($model->record_id);
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
                return Html::a('删除',$url,[ 'data-toggle'=>'modal','class'=>'delete','data-target'=>'#contact-modal','style'=>'margin-left:10px']);
            },
        ],
    ],
];

echo \yii\bootstrap\Alert::widget([
    'body'=>'等级段，开始等级必须小于结束等级，等级段之间请不要有包含关系。例如：已经新增了一个 10-20 等级段， 则不能新增或修改一个 15-25 等级段',
    'options'=>[
        'class'=>'alert-warning',
    ]
]);

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
            'content'=> Html::button($add_title,['type'=>'button','title'=>$add_title, 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('enterroomnote/create').'";return false;']),
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
$("#goods_list-pjax").on("click",".delete",function(){
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