<style>
    .back-a{
        display: inline-block;
        font-size: 14px;
        border-radius: 3px;
        color: #00a7d0;
        border:1px solid #00a7d0;
        padding: 3px 5px;
    }
    .back-btn{
        display: inline-block;
        font-size: 14px;
        margin-bottom: 0px;
        border-radius: 3px;
        color: #00a65a;
        border:1px solid #00a65a;
        padding: 3px 3px;
    }
</style>
<?php

use yii\bootstrap\Html;
use kartik\grid\GridView;

/**
 *  @var $dataProvider
 *  @var $searchModel \backend\models\CustomerSearch
 *  @var $model common\models\Client
 *  @var $is_verify
 */

$gridColumns = [
    [
        'attribute'=>'client_id',
        'vAlign'=>'middle',
        'width'=>'80px'
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'width'=>'200px',
    ],
    [
        'attribute'=>'open_id',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'sex',
        'vAlign'=>'middle',
        'width'=>'10%',
        'value'=>function($model) {
            switch($model->sex) {
                case 1: $rst = '男';break;
                case 2: $rst = '女';break;
                default: $rst = '未知';break;
            }
            return $rst;
        },
        'filter'=>['1'=>'男','2'=>'女'],
    ],
    [
        'attribute'=>'province',
        'vAlign'=>'middle',
        'width'=>'80px',
    ],
    [
        'attribute'=>'city',
        'vAlign'=>'middle',
        'width'=>'80px',
    ],
    [
        'attribute'=>'update_time',
        'vAlign'=>'middle',
        'label'=>'消息时间',
        'width'=>'150px',
        'filter'=>false
    ],
    [
        'class'=> 'kartik\grid\ActionColumn',
        'template' => '{send_msg}',
        'dropdown' => false,
        'vAlign' => 'middle',
        'width' => '100px',
        'urlCreator' => function($action, $model, $key, $index) {
            $url = '';
            switch($action) {
                case 'send_msg':
                    $url = '/template/msg_template?user_id='.strval($model->client_id);
                    break;
            }
            return $url;
        },
        'viewOptions' => ['title' => '查看', 'data-toggle'=> 'tooltip'],
        'buttons'=>[
            'send_msg'=>function($url, $model) {
                return Html::a('回复消息', $url, ['style'=>'margin-right:10px','class'=>'back-a']);
            }
        ]
    ]
];

echo GridView::widget([
    'id'=>'customer_list',
    'dataProvider' => $dataProvider,
    'filterModel'=>$searchModel,
    'columns'=>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('群发消息', ['type'=>'button', 'class'=>'btn btn-primary', 'onclick'=>'location="'.Yii::$app->urlManager->createUrl('template/create_batch_msg').'"; return false;'])
        ],
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']
    ],
    'pjax' => true,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'panel' => [
        'type' => GridView::TYPE_INFO
    ],
]);

$js = '';
$this->registerJs($js, \yii\web\View::POS_END);