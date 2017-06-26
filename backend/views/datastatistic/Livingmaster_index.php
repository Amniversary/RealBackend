<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 16:21
 */

\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;
use frontend\business\ActivityUtil;

$gridColumns = [
    [
        'attribute'=>'share_date',
        'vAlign'=>'middle',
        'width'=>'60px',
        'label'=>'日期'
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'width'=>'60px',
        'label'=>'蜜播ID'
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'width'=>'60px',
        'label'=>'用户昵称'
    ],
    [
         'attribute'=>'living_master_share_no',
         'vAlign'=>'middle',
         'width'=>'60px',
         'label'=>'主播分享次数'
    ],
    [
        'attribute'=>'audience_share_no',
        'vAlign'=>'middle',
        'width'=>'60px',
        'label'=>'观众分享次数'
    ],
    [
        'attribute'=>'total_no',
        'vAlign'=>'middle',
        'width'=>'60px',
        'label'=>'总次数'
    ],
];

echo \yii\bootstrap\Alert::widget([
    'body'=>'温馨提示：日期搜索请按照格式搜索，格式为：YYYY-MM-DD 例如：2016-08-20 ，查询某个时间段的日期格式为YYYY-MM-DD_YYYY-MM-DD 例如：2016-03-21_2016-08-20。',
    'options'=>[
        'class'=>'alert-warning',
    ]
]);


echo GridView::widget([
    'id'=>'params_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[
        [
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
//    'toolbar'=> [
//        [
//            'content'=> Html::button('新增活动',['type'=>'button','title'=>'新增活动', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('scoregift/create').'";return false;']),
//
//        ],
//        '{export}',
//        '{toggleData}',
//        'toggleDataContainer' => ['class' => 'btn-group-sm'],
//        'exportContainer' => ['class' => 'btn-group-sm']
//
//    ],
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

';
$this->registerJs($js,\yii\web\View::POS_END);




