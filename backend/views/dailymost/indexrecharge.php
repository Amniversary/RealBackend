<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/29
 * Time: 19:06
 */

use kartik\grid\GridView;
use \yii\bootstrap\Tabs;

echo Tabs::widget([
    'options'=>['class'=>'ctr-head'],
    'items' => [
        [
            'label' => '票数榜',
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['dailymost/index']),
            'active' => ($data_type === 'audited'? true: false),
            'options' => ['id' => 'my_comment_undo'],
        ],
        [
            'label' => '充值榜',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['dailymost/indexrecharge']),
            'headerOptions' => [],
            'options' => ['id' => 'my_comment_his'],
            'active' => ($data_type === 'recharge'? true: false)
        ],
        [
            'label' => '送礼榜',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['dailymost/indexgift']),
            'headerOptions' => [],
            'options' => ['id' => 'my_comment_his'],
            'active' => ($data_type === 'gift'? true: false)
        ],
    ],
]);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'recharge_date',
        'vAlign'=>'middle',
        'label' =>'充值日期',
        'width' => '400px',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label' =>'密播 ID',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'昵称',
    ],
    [
        'attribute'=>'recharge_amount',
        'vAlign'=>'middle',
        'label' =>'充值金额',
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


$js='';
$this->registerJs($js,\yii\web\View::POS_END);