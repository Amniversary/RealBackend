<style>
    .page-read {
        color: red;
    }
    .alert{
        padding: 10px;
    }
    .content-header {
        position: relative;
        padding: 1px 15px 0 15px;
    }
</style>
<?php
use kartik\grid\GridView;
use yii\bootstrap\Html;

/**
 * @var $model common\models\ArticleTotal
 * @var $searchModel \backend\models\ArticleStatisticSearch
 */

echo \yii\bootstrap\Alert::widget([
    'body' => '根据公众号 ID 进行查询, ID 可从公众号列表复制 !',
    'options' => [
        'class' => 'alert-warning',
    ]
]);
$gridColumns = [
//    ['class' => 'kartik\grid\SerialColumn'],
    [
        'label' => '#',
        'attribute' => 'app_id',
        'vAlign' => 'middle',
        'width' => '70px',
    ],
    [
        'attribute' => 'app_id',
        'vAlign' => 'middle',
        'width' => '100px',
        'value' => function ($model) {
            return \common\models\AttentionEvent::getKeyAppId($model->app_id);
        },
        'filter' => false
    ],
    [
        'attribute' => 'stat_date',
        'vAlign' => 'middle',
        'width' => '120px',
        'filterType' => '\yii\jui\DatePicker',
        'filterWidgetOptions' => [
            'language' => 'zh-CN',
            'dateFormat' => 'yyyy-MM-dd',
            'options' => ['class' => 'form-control', 'style' => 'display:inline-block;']
        ],
    ],
    [
        'attribute' => 'title',
        'vAlign' => 'middle',
        'value' => function ($model) {
            $len = strlen($model->title);
            return $len > 20 ? mb_substr($model->title, 0, 15) . '...' : $model->title;
        },
        'filter' => false
    ],
    [
        'attribute' => 'target_user',
        'vAlign' => 'middle',
        'filter' => false
    ],
    [
        'attribute' => 'page_read_rate',
        'vAlign' => 'middle',
        'format' => 'html',
        'value' => function ($model) {
            return $model->page_read_rate . '%';
        },
        'filter' => false
    ],
    [
        'attribute' => 'int_page_read_user',
        'vAlign' => 'middle',
        'filter' => false
    ],
    [
        'attribute' => 'int_page_read_count',
        'vAlign' => 'middle',
        'filter' => false
    ],
    [
        'attribute' => 'share_rate',
        'vAlign' => 'middle',
        'value' => function ($model) {
            return $model->share_rate . '%';
        },
        'filter' => false
    ],
    [
        'attribute' => 'share_user',
        'vAlign' => 'middle',
        'filter' => false,
    ],
    [
        'attribute' => 'share_count',
        'vAlign' => 'middle',
        'filter' => false
    ],
    [
        'attribute' => 'ori_page_read_rate',
        'vAlign' => 'middle',
        'value' => function ($model) {
            return $model->ori_page_read_rate . '%';
        },
        'filter' => false
    ],
    [
        'attribute' => 'ori_page_read_user',
        'vAlign' => 'middle',
        'filter' => false,
    ],
    [
        'attribute' => 'ori_page_read_count',
        'vAlign' => 'middle',
        'filter' => false
    ],
    [
        'attribute' => 'add_to_fav_rate',
        'vAlign' => 'middle',
        'value' => function ($model) {
            return $model->add_to_fav_rate . '%';
        },
        'filter' => false
    ],
    [
        'attribute' => 'add_to_fav_user',
        'vAlign' => 'middle',
        'filter' => false,
    ],
    [
        'attribute' => 'add_to_fav_count',
        'vAlign' => 'middle',
        'filter' => false,
    ],
];

echo GridView::widget([
    'id' => 'article_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto;height:750px;font-size:14px;'],
    'beforeHeader' => [['options' => ['class' => 'skip-export']]],
    'toolbar' => [
        [
            'content' => '',
        ],
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']
    ],
    'bordered' => true,
    'striped' => true,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'panel' => ['type' => GridView::TYPE_INFO],
]);