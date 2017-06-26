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

    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'width'=>'200px',
        'label'=>'蜜播ID',
    ],

    [
        'attribute'=>'name',
        'vAlign'=>'middle',
        'label'=>'姓名',
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label'=>'状态',
        'filter'=>['1'=>'用户提交审核','2'=>'审核不通过','3'=>'审核通过'],
        'value'=>function($model){
            return   GetStatus($model['status']);
        },
        'editableOptions'=>function($model){
            return [
                'formOptions'=>['action'=>'/wxlivemanage/status?id='.strval($model['id'])],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'用户提交审核','2'=>'审核不通过','3'=>'审核通过'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'create_at',
        'vAlign'=>'middle',
        'label'=>'创建时间',
        'value'=>function($model){
            return  date('Y-m-d H:i:s',$model['create_at']);
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'width'=>'250px',
        'template'=>'{update}',
        'vAlign'=>'middle',

        'buttons'=>[
            'update'=>function($url,$model){
                if($model['status'] != 3){
                    return Html::a('删除','/wxlivemanage/delete?id='.strval($model['id']),['title'=>'删除','class'=>'delete','data-toggle'=>false,'style'=>'margin-left:10px']);
                }
                return '';
            }
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


function GetStatus($status){
    if( $status == 1 ){
        return  '用户提交审核';
    }else if( $status == 2 ){
        return '审核不通过';
    }else if( $status == 3 ){
        return '审核通过';
    }
}