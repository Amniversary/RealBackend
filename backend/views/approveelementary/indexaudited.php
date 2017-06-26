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
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['approveelementary/index']),// $this->render('indexundo'),
            'active' => ($data_type === 'check'? true: false),
            'options' => ['id' => 'my_comment_undo'],
        ],
        [
            'label' => '已审核',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['approveelementary/indexaudited']),//$this->render('indexhis'),
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
        'label' => '蜜播ID',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label' => '用户昵称',
        'width'=>'220px',
    ],
    [
        'attribute'=>'register_type',
        'vAlign'=>'middle',
        'label' => '手机类型',
        'width'=>'150px',
        'value'=>function($model)
        {
            $register_type = '';
            switch($model['register_type'])
            {
                case 1:
                    $register_type = '手机';
                    break;
                case 2:
                    $register_type = '微信';
                    break;
                case 3:
                    $register_type = '新浪微博';
                    break;
                case 4:
                    $register_type = 'QQ';
                    break;
                case 5:
                    $register_type = '后台机器人注册';
                    break;
            }
            return $register_type ;
        },
        'filter'=>['1'=>'手机','2'=>'微信','3'=>'新浪微博','4'=>'QQ','5'=>'后台机器人注册'],
    ],
    [
        'attribute'=>'phone_no',
        'vAlign'=>'middle',
        'label' => '手机号',
        'width'=>'150px',
    ],
    [
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label' => '状态',
        'width'=>'100px',
        'value'=>function($model)
        {
            return $model['status'] = ($model['status']==0?'未审核':($model['check_result_status']==0?'已拒绝':'已审核'));;
        },
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'label' => '创建时间',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'attribute'=>'check_time',
        'vAlign'=>'middle',
        'label' => '审核时间',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'attribute'=>'check_user_name',
        'vAlign'=>'middle',
        'label' => '审核人昵称',
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
                    $url = '/approvebusinesscheck/detail?date_type=audited&approve_id='.strval($model['approve_id']);

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
    $(function(){
        $("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
    });
';
$this->registerJs($js,\yii\web\View::POS_END);