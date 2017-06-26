<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 13:55
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;


$gridColumns = [

    [
        'attribute'=>'user_id',
        'vAlign'=>'middle',
        'label'=>'用户ID',
         'width'=>'109px',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label'=>'蜜播ID',
         'width'=>'109px',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'用户名称',
         'width'=>'260px',
    ],
    [
        'attribute'=>'integral_account_total',
        'vAlign'=>'middle',
        'label'=>'积分汇总',
         'width'=>'109px',
    ],
    [
        'attribute'=>'integral_account_spend',
        'vAlign'=>'middle',
        'label'=>'积分支出汇总',
         'width'=>'129px',
    ],
    [
        'attribute'=>'integral_account_balance',
        'vAlign'=>'middle',
        'label'=>'积分帐户余额',
         'width'=>'129px',
    ],
    [   'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'account_status',
        'vAlign'=>'middle',
        'width'=>'80px',
        'label'=>'帐户状态',
        'value'=>function($model){
            return \backend\models\UserGoldsAccountForm::GetGoldsAccountStatus($model['account_status']);
        },
        'filter'=>['1'=>'正常','2'=>'冻结','3'=>'异常'],
        'editableOptions'=>function($model){
            return [
                'formOptions'=>['action'=>'/integralaccount/status?integral_account_id='.strval($model['integral_account_id'])],
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
        'width'=>'150px',
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
            'update'=>function($url,$model)
            {
                if($model['account_status'] == 1)
                {
                    return Html::a('帐户明细','javascript:;',['class'=>'money_detail','data-src'=>'/integralaccount/detail?integral_account_id='.strval($model['integral_account_id'])])."&nbsp;&nbsp;&nbsp;". Html::a('充值','/integralaccount/edit?integral_account_id='.strval($model['integral_account_id']),[ ]);
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