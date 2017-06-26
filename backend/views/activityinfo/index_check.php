<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/26
 * Time: 11:48
 */
\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;
use \yii\bootstrap\Tabs;

echo Tabs::widget([
    'options'=>['class'=>'ctr-head'],
    'items' => [
        [
            'label' => '未审核',
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['activityinfo/enroll_index']),// $this->render('indexundo'),
            'active' => ($data_type === 'check'? true: false),
            'options' => ['id' => 'my_comment_undo'],
        ],
        [
            'label' => '已审核',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['activityinfo/enroll_already']),//$this->render('indexhis'),
            'headerOptions' => [],
            'options' => ['id' => 'my_comment_his'],
            'active' => ($data_type === 'already'? true: false)
        ],
    ],
]);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
//    [
//        'class' => 'kartik\grid\ActionColumn',
//        'template'=>'{select_all}',
//        'dropdown' => false,
//        'vAlign'=>'middle',
//        'buttons'=>[
//            'select_all' => function ($url, $model, $key)
//            {
//                return Html::checkbox(false,'',['class'=>'select_check','value'=>strval($model['approve_id'])]);
//            },
//        ],
//    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label' => '蜜播ID',
    ],
    [
        'attribute'=>'name',
        'vAlign'=>'middle',
        'label' => '姓名',
    ],
    [
        'attribute'=>'phone_number',
        'vAlign'=>'middle',
        'label' => '手机号',
    ],
    [
        'attribute'=>'sex',
        'vAlign'=>'middle',
        'label' => '性别',
        'filter' =>['男' => '男','女' => '女']
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label' => '审核',
        'value' => function($model)
        {
            switch($model->status)
            {
                case 0:
                    return '未审核';
                    break;
                case 1:
                    return '已拒绝';
                    break;
                case 2:
                    return '已通过';
                    break;
            }
        },
        'editableOptions'=>function($model)
        {
            return [
                'header' =>'审核',
                'formOptions'=>['action'=>'/activityinfo/set_status?enroll_id='.strval($model->enroll_id)],
                'size'=>'sm',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'拒绝','2'=>'通过'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'label' => '报名时间',
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
$js = '
$(function(){
        $("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
    });
';
$this->registerJs($js,\yii\web\View::POS_END);