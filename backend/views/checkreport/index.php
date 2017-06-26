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
            'label' => '未审核',
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['checkreport/index']),
            'active' => ($data_type === 'check'? true: false),
            'options' => ['id' => 'my_comment_undo'],
        ],
        [
            'label' => '已审核',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['checkreport/indexaudited']),
            'headerOptions' => [],
            'options' => ['id' => 'my_comment_his'],
            'active' => ($data_type === 'audited'? true: false)
        ],
    ],
]);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label' =>'举报人账号',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'举报发起人昵称',
    ],

    [
        'attribute'=>'scene',
        'label'=>'场景',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return ($model['scene'] == '2')?'群':(($model['scene'] == '3')?'好友':'其他');
        },
        'filter'=>['2'=>'群','3'=>'好友'],
    ],
    [
        'attribute'=>'report_type',
        'label' => '举报类型',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            $type = '';
            switch($model['report_type']){
                case 1:
                    $type = '欺诈';
                    break;
                case 2:
                    $type = '色情';
                    break;
                case 3:
                    $type = '政治谣言';
                    break;
                case 4:
                    $type = '常识性谣言';
                    break;
                case 5:
                    $type = '恶意营销';
                    break;
                case 6:
                    $type = '其他侵权';
                    break;
                default:
                    $type = '未知类型';
                    break;
            }
            return $type;
        },
        'filter'=>['1'=>'欺诈','2'=>'色情','政治谣言','4'=>'常识性谣言','5'=>'恶意营销','6'=>'其他侵权'],
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label'=>'状态',
        'value'=>function($model)
        {
            return (($model['status'] == 1)? '正常':'禁用');
        },
        'filter'=>['1'=>'正常','0'=>'禁用'],
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'status',
                'value'=>$model['status'],
                'formOptions'=>['action'=>'/checkreport/set_status?client_id='.strval($model['report_user_id'])],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'正常','0'=>'禁用'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'report_client_no',
        'vAlign'=>'middle',
        'label' =>'被举报人账号',
    ],
    [
        'attribute'=>'report_user_name',
        'vAlign'=>'middle',
        'label' =>'被举报人昵称',
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
        'label'=> '创建时间',
    ],
    [
        'attribute'=>'check_time',
        'vAlign'=>'middle',
        'label' =>'审核时间',
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
                    $url = '/checkreport/detail?date_type=check&report_id='.strval($model['report_id']);

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