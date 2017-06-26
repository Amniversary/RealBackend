<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:38
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;
use common\models\RedPackets;


$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'packets_name',
        'vAlign'=>'middle',
    ],
//    [
//        'class' => 'kartik\grid\EditableColumn',
//        'attribute' => 'packets_name',
//        //'pageSummary' => 'Page Total',
//        'vAlign'=>'middle',
//        'headerOptions'=>['class'=>'kv-sticky-column'],
//        'contentOptions'=>['class'=>'kv-sticky-column'],
//        'editableOptions'=>['header'=>'Name', 'size'=>'md']
//    ],
//    [
//        'attribute'=>'discribtion',
//        'value'=>function ($model, $key, $index, $widget) {
//            return "<span class='badge' style='background-color: {$model->discribtion}'> </span>  <code>" .
//            $model->discribtion . '</code>';
//        },
//        'filterType'=>GridView::FILTER_COLOR,
//        'vAlign'=>'middle',
//        'format'=>'raw',
//        'width'=>'150px',
//        'noWrap'=>true
//    ],
    [
        'attribute'=>'packets_type',
        'value'=>function($model)
        {
            return $model->GetPacketsTypeName();
        },
        'filter'=>array('64'=>'打赏奖励红包','256'=>'打赏愿望奖励红包','260'=>'签到红包'),
    ],
    [
        //'class'=>'kartik\grid\BooleanColumn',
        'format'=>'html',
        'attribute'=>'pic',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            $url = empty($model->pic)?\Yii::$app->params['red_packet_pic']['ok']:$model->pic;
            return Html::img($url,['style'=>'width:100px;']);
        },
        'filter'=>false
    ],
    [
        //'class'=>'kartik\grid\BooleanColumn',
        'format'=>'html',
        'attribute'=>'over_pic',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            $url = empty($model->over_pic)?\Yii::$app->params['red_packet_pic']['overtime']:$model->over_pic;
            return Html::img($url,['style'=>'width:100px;']);
        },
        'filter'=>false
    ],
    'packets_money',
    [
        'attribute'=>'get_type',
        'value'=>function ($model)
        {
            return $model->GetGetTypeName();
        },
        'vAlign'=>'middle',
        'filter'=>['1'=>'领取后N天过期','2'=>'设置过期日期'],
    ],
    'overtime_days',
    'start_time',
    'end_time',
    [
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;&nbsp;&nbsp;{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/redpacket/modify?red_packets_id='.strval($model->red_packets_id);
                    break;
                case 'delete':
                    $url = '/redpacket/delete?red_packets_id='.strval($model->red_packets_id);
            }
            return $url;
        },
        'viewOptions'=>['title'=>'查看', 'data-toggle'=>'tooltip'],
        'updateOptions'=>['title'=>'编辑', 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['title'=>'删除', 'data-toggle'=>'tooltip'],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:500px;'], // only set when $responsive = false
    'beforeHeader'=>[
        [
//            'columns'=>[
//                ['content'=>'Header Before 1', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 2', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 3', 'options'=>['colspan'=>3, 'class'=>'text-center warning']],
//            ],
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar' =>  [
        ['content'=>
            Html::button('新增红包', ['type'=>'button', 'title'=>'新增红包', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('redpacket/create').'";return false;']),// . ' '.
            //Html::a('&lt;i class="glyphicon glyphicon-repeat">&lt;/i>', ['grid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
        ],
        '{export}',
        '{toggleData}',
            'toggleDataContainer' => ['class' => 'btn-group-sm'],
    'exportContainer' => ['class' => 'btn-group-sm']
    ],
    'pjax' => true,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    //'floatHeader' => true,
    //'floatHeaderOptions' => ['scrollingTop' => '300px'],
    //'showPageSummary' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);