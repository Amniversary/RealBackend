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
        'attribute'=>'living_id',
        'label' => '直播ID',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'room_no',
        'label' => '房间号',
        'vAlign'=>'middle',
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'status',
        'label' => '开启/关闭',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return (($model['status'] != 2) ? '关闭':'开启');
        },
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/falseliving/setstatus?living_id='.strval($model['living_id'])],
                'size'=>'sm',
                'name'=>'status',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['2'=>'正常','0'=>'禁用'],
            ];
        },
        'refreshGrid'=>true,
        'filter'=>['0'=>'关闭','2'=>'开启'],
    ],
    [
        'attribute'=>'ticket_num',
        'label' => '收到的门票数',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'flower_num',
        'label' => '收到的鲜花数',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'system_num',
        'label' => '收到的总数',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'create_time',
        'label' => '创建时间',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
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
            'content'=> Html::button($add_title,['type'=>'button','title'=>$add_title, 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('falseliving/create').'";return false;']),
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