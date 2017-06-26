<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/19
 * Time: 13:30
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
use \yii\bootstrap\Tabs;


echo Tabs::widget([
    'options'=>['class'=>'ctr-head'],
    'items' => [
        [
            'label' => '未审核',
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['changerecord/index']),// $this->render('indexundo'),
            'active' => ($data_type === 'noexamine'? true: false),
            'options' => ['id' => 'my_comment_undo'],
        ],
        [
            'label' => '已审核',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['changerecord/indexexamine']),//$this->render('indexhis'),
            'headerOptions' => [],
            'options' => ['id' => 'my_comment_his'],
            'active' => ($data_type === 'examine'? true: false)
        ],
    ],
]);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'user_id',
        'vAlign'=>'middle',
        'label' =>'用户ID',
        'width'=>'150px',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label'=>'密播ID',
        'width'=>'150px',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=> 'middle',
        'label'=>'密播昵称',
        'width'=>'100px'
    ],
    [
        'attribute'=>'user_name',
        'vAlign'=>'middle',
        'label'=>'用户姓名',
        'width'=>'100px',
    ],
    [
        'attribute'=>'gift_name',
        'vAlign'=>'middle',
        'label'=>'兑换的商品',
        'width'=>'150px',
    ],
    [
        'attribute'=>'change_time',
        'vAlign'=>'middle',
        'label'=>'兑换的时间',
        'width'=>'250px',
    ],
    [
        'attribute'=>'change_state',
        'vAlign'=>'middle',
        'label'=>'发货的状态',
        'width'=>'150px',
        'value'=>function($model)
        {
            switch($model['change_state'])
            {
                case 0:
                    $ss = '未发货';
                    break;
                case 1:
                    $ss = '已发货';
                    break;
                case 2:
                    $ss = '已拒绝';
                    break;
            }
            return $ss;
        },
        'filter'=>['0'=>'未发货','1'=>'已发货','2'=>'已拒绝'],
    ],
    [
        'attribute'=>'address',
        'vAlign'=>'middle',
        'label'=>'用户的地址',
        'width'=>'500',
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
';
$this->registerJs($js,\yii\web\View::POS_END);