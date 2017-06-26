<style>
    .user-pic
    {
        width: 60px;
    }
    .check-item
    {
        margin-right: 10px;
    }
    .form-control.my-input
    {
        display: inline;
        width: auto;
    }
</style>

<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 10:32
 */
\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;
$gridColumns = [
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label'=>'蜜播ID',
        'width'=>'200px',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'用户呢称',
        'width'=>'300px',
    ],
    [
        'attribute'=>'phone_no',
        'vAlign'=>'middle',
        'label'=>'用户手机号',
    ],
    [
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label'=>'直播间状态',
        'width'=>'200px',
        'value'=>function($model)
        {
            if($model['status'] == 1 ){
                return '已禁播';
            }else  {
                return '正常';
            }
        },
        'filter'=>['0'=>'正常','1'=>'已禁播'],
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'remark1',
        'vAlign'=>'middle',
        'label'=>'操作',
        'width'=>'200px',
        'value'=>function($model)
        {
            if($model['status'] == 1 ){
                return '解禁';
            }else  {
                return '禁播';
            }
        },
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'remark1',
                'formOptions'=>['action'=>'/stopliving/status?stop_id='.$model['stop_id'].'&status='.$model['status']],
                'size'=>'md',
                'value'=>'',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                'options' => ['placeholder' => '请输入原因'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'create_date',
        'vAlign'=>'middle',
        'label'=>'创建时间',
        'width'=>'200px',
    ],

];

echo GridView::widget([
    'id'=>'living_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[
        [
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
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



$js = '
$(function(){
$("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
});

$(document).on("click", ".money_detail", function(){
    var src = $(this).data("src");
    $("#myModal3").modal("show");
    $("#myFrame3").attr("src", src);
});

';
$this->registerJs($js,\yii\web\View::POS_END);
?>

<style>
    .myModal .modal-dialog{
        width: 1200px;
        height: 880px;
        overflow: hidden;
    }
    .myModal .modal-body{
        padding: 0;
    }
    iframe{
        border: none;
        width: 100% !important;
        height: 780px !important;
    }
</style>
<!-- 余额情Modal -->
<div class="modal fade myModal" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <iframe id="myFrame3" name="myFrame3" style="width:900px; height:500px;"></iframe>
            </div>
        </div>
    </div>
</div>
