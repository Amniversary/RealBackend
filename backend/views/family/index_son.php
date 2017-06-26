<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 15:33
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'],
    [
        'attribute'=>'client_no',
        'label' => '蜜播ID',
        'vAlign'=>'middle',
        'width' => '160px',
    ],
    [
        'attribute'=>'nick_name',
        'label' => '成员昵称',
        'vAlign'=>'middle',
        'width' => '160px',
    ],
    [
        'attribute'=>'icon_pic',
        'label' => '成员头像',
        'vAlign'=>'middle',
        'format'=>'html',
        'width' => '120px',
        'hiddenFromExport' => true,
        'value'=>function($model)
        {
            $url = $model['icon_pic'];
            return Html::img($url,['class'=>'pic','style'=>'width:50px']);
        }
    ],
    [
        'attribute'=>'status',
        'label' => '帐号状态',
        'vAlign'=>'middle',
        'width' => '160px',
        'value'=>function($model)
        {
            if ($model['status'] != 1) {
                return '已封号';
            }
            if ($model['stop_status'] == 1) {
                return '已禁播';
            }
            return '正常';
        }
    ],
    [
        'attribute'=>'finish_time',
        'label' => '最近直播时间',
        'vAlign'=>'middle',
        'width' => '160px',
    ],
    [
        'attribute'=>'ticket_count',
        'label' => '可提现票数',
        'vAlign'=>'middle',
        'width' => '160px',
    ],
    [
        'attribute'=>'ticket_count_sum',
        'label' => '累计票数(不含虚拟礼物)',
        'vAlign'=>'middle',
        'width' => '180px',
    ],
    [
        'attribute'=>'create_time',
        'label' => '成员加入时间',
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
        'attribute'=>'remark1',
        'label' => '操作员',
        'vAlign'=>'middle',
        'width' => '180px',
    ],
    [
        'attribute'=>'living_time',
        'label' => '直播时长',
        'vAlign'=>'middle',
        'width' => '350px',
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
        ],
        'value'=>function($model) {
            return empty($model['living_time']) ? 0 : $model['living_time'];
        }
    ],
    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'&nbsp;&nbsp;{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'delete':
                    $url = '/family/delete_son?record_id='.strval($model['record_id']);
                    break;

            }
            return $url;
        },
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'buttons'=>[
            'delete' =>function ($url, $model,$key)
            {
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-pjax'=>'0','style'=>'margin-left:10px']);
            },
        ],
    ],
];

echo GridView::widget([
    'id'=>'family_list',
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
            'content'=> Html::button('返回家族列表',['type'=>'button','title'=>'返回家族列表', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl(['family/index','page'=>$page]).'";return false;']),
        ],
        [
            'content'=> Html::button('新增成员',['type'=>'button','data-target'=>'#contact-modal','title'=>'新增成员', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl(['family/create_son','family_id'=>$family_id,'page'=>$page]).'";return false;']),
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
$("#family_list-pjax").on("click",".delete",function(){
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
                    $("#family_list").yiiGridView("applyFilter");
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