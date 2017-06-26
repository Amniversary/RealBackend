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
 * Date: 2016/6/27
 * Time: 13:55
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;
use backend\models\GoldsAccountSearch;

$gridColumns = [

    [
        'attribute'=>'user_id',
        'vAlign'=>'middle',
          'width'=>'109px',
        'label'=>'用户ID',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
          'width'=>'109px',
        'label'=>'蜜播ID',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle', 
        'label'=>'用户名称',
       
    ],
    [
        'attribute'=>'gold_account_total',
        'vAlign'=>'middle',
         'width'=>'130px',
        'label'=>'金币汇总',
    ],
    [
        'attribute'=>'gold_account_expend',
        'vAlign'=>'middle',
         'width'=>'130px',
        'label'=>'金币支出汇总',
    ],
    [
        'attribute'=>'gold_account_balance',
        'vAlign'=>'middle',
         'width'=>'130px',
        'label'=>'帐户余额',
    ],
    [   'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'account_status',
        'vAlign'=>'middle',
        'label'=>'帐户状态',
        'value'=>function($model){
            return \backend\models\UserGoldsAccountForm::GetGoldsAccountStatus($model['account_status']);
        },
        'filter'=>['1'=>'正常','2'=>'冻结','3'=>'异常'],
        'editableOptions'=>function($model){
            return [
                'formOptions'=>['action'=>'/goldsaccount/status?gold_account_id='.strval($model['gold_account_id'])],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'正常','2'=>'冻结','3'=>'异常'],
            ];
         },
        'refreshGrid'=>true, 
    ],
    [
        'attribute'=>'create_time',
        'label'=>'创建时间',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
         'width'=>'170px',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'width'=>'250px',
        'template'=>'{update}',
        'vAlign'=>'middle',
        'updateOptions'=>['title'=>'帐户明细','label'=>'帐户明细', 'data-toggle'=>false],
   
        'buttons'=>[
            'update'=>function($url,$model){
                if($model['account_status'] == 1){
                    //return Html::a('帐户明细',$url,[ ])."&nbsp;&nbsp;&nbsp;". Html::a('充值','/goldsaccount/edit?gold_account_id='.strval($model['gold_account_id']));
                    return Html::a('帐户明细','javascript:;',['class'=>'money_detail','data-src'=>'/goldsaccount/detail?gold_account_id='.strval($model['gold_account_id']).'&user_id='.$model['user_id']])."&nbsp;&nbsp;&nbsp;". Html::a('充值','/goldsaccount/edit?gold_account_id='.strval($model['gold_account_id']))
                           ."&nbsp;&nbsp;&nbsp;".Html::a('回调','/goldsaccount/back?gold_account_id='.strval($model['gold_account_id']));
                }
                return '';
            }
        ],
    ],

];
echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

echo GridView::widget([
    'id'=>'recharge_list',
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
