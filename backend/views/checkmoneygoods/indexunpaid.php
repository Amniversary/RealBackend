<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:48
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
use \yii\bootstrap\Tabs;

echo Tabs::widget([
    'options'=>['class'=>'ctr-head'],
    'items' => [
        [
            'label' => '未打款',
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['checkmoneygoods/indexcash','data_type'=>'unpaid']),// $this->render('indexundo'),
            'active' => ($data_type === 'unpaid'? true: false),
            'options' => ['id' => 'my_comment_undo'],
        ],
        [
            'label' => '已打款',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['checkmoneygoods/indexcash','data_type'=>'alreadypaid']),//$this->render('indexhis'),
            'headerOptions' => [],
            'options' => ['id' => 'my_comment_his'],
            'active' => ($data_type === 'alreadyunpaid'? true: false)
        ],
    ],
]);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'client_id',
        'vAlign'=>'middle',
        'label' => 'ID',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label' => '蜜播ID',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label' => '用户名',
    ],
    [
        'attribute'=>'ticket_num',
        'vAlign'=>'middle',
        'label' => '票数',
    ],
    [
        'attribute'=>'cash_type',
        'vAlign'=>'middle',
        'label' => '支付方式',
        'value'=>function($model)
        {
            return ($model['cash_type'] == '1')?'微信':(($model['cash_type'] == '2')?'支付宝':'其他');
        },
        'filter'=>['1'=>'微信','2'=>'支付宝'],
    ],
    [
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label' => '状态',
        'value'=>function($model)
        {
            return ($model['status'] == '1')?'未审核':(($model['status'] == '2')?'未打款':(($model['status'] == '3')?'已打款':'拒绝'));
        },
        'filter'=>['1'=>'已受理','2'=>'已审核','3'=>'打款','4'=>'拒绝'],
    ],
//    [
//        'class'=>'kartik\grid\EditableColumn',
//        'label' => '状态',
//        'attribute'=>'status',
//        'vAlign'=>'middle',
//        'value'=>function($model)
//        {
//            return ($model['status'] == '1')?'未审核':(($model['status'] == '2')?'未打款':(($model['status'] == '3')?'已打款':'拒绝'));
//        },
//        'filter'=>['1'=>'已受理','2'=>'已审核','3'=>'打款','4'=>'拒绝'],
//        'editableOptions'=>function($model)
//        {
//            return [
//                'name'=>'status',
//                //'formOptions'=>['action'=>'/checkmoneygoods/setstatus?record_id='.strval($model['record_id'])],
//                'header'=>'审核状态',
//                'size'=>'md',
//                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
//                'displayValueConfig'=>['1'=>'已受理','2'=>'已审核','3'=>'打款','4'=>'拒绝'],
//                'data'=>['1'=>'已受理','2'=>'已审核','3'=>'打款','4'=>'拒绝'],
//            ];
//        },
//    ],
    [
        'attribute'=>'real_cash_money',
        'vAlign'=>'middle',
        'label' => '除手续费后的提现金额',
    ],
//    [
//        'attribute'=>'refuesd_reason',
//        'vAlign'=>'middle',
//        'label' => '拒绝原因',
//    ],
//    [
//        'attribute'=>'finance_remark',
//        'vAlign'=>'middle',
//        'label' => '打款备注',
//    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'label' => '创建时间',
    ],
    [
        'attribute'=>'check_time',
        'vAlign'=>'middle',
        'label' => '审核时间',
    ],
    [
        'attribute'=>'finace_ok_time',
        'vAlign'=>'middle',
        'label' => '打款时间',
    ],
    [
        'width'=>'120px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/checkmoneygoods/detailcash?date_type=unpaid&record_id='.strval($model['record_id']);

            }
            return $url;
        },
        'updateOptions'=>['title'=>'查看详情','label'=>'查看详情', 'data-toggle'=>false],
        'buttons'=>[
            'update' => function ($url, $model, $key)
            {
                return Html::a('查看详情',$url,['data-toggle'=>'modal', 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
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
$("#check_goods_delete").on("click",function(){
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


$(function(){
        $("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
    });
';
$this->registerJs($js,\yii\web\View::POS_END);