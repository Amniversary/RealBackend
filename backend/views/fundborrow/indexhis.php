<style>
    .ctr-head
    {
        margin-bottom: 10px;
    }
</style>
<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:38
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;
use \yii\bootstrap\Tabs;

echo Tabs::widget([
    'options'=>['class'=>'ctr-head'],
    'items' => [
        [
            'label' => '未打款借款记录',
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['fundborrow/index','data_type'=>'undo']),// $this->render('indexundo'),
            'active' => ($data_type === 'undo'? true: false),
            'options' => ['id' => 'my_fund_borrow_undo'],
        ],
        [
            'label' => '已打款借款记录',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['fundborrow/indexhis','data_type'=>'his']),//$this->render('indexhis'),
            'headerOptions' => [],
            'options' => ['id' => 'my_fund_borrow_his'],
            'active' => ($data_type === 'his'? true: false)
        ],
    ],
]);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'borrow_fund_id',
        'width'=>'100px',
    ],
    [
        'attribute'=>'borrow_money',
        'width'=>'100px',
    ],
    [
        'attribute'=>'status_result',
        'vAlign'=>'middle',
        'value'=>function($model)
        {

            return $model->GetStatusName();
        },
        'filter'=>['4'=>'已打款','8'=>'被拒绝'],
    ],
    'finance_remark',
    'identity_no',
    [
        'attribute'=>'user_name',
        'width'=>'80px',
    ],
    'card_no',
    'bank_name',
    [
        'label'=>'类型',
        'attribute'=>'borrow_type',
        'value'=>function($model)
        {
            return $model->GetBorrowTypeName();
        },
    ],
    [
        //'class'=>'kartik\grid\BooleanColumn',
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            //'attribute'=>'start_time',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:620px;'], // only set when $responsive = false
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
        ['content'=>'',
            //Html::a('&lt;i class="glyphicon glyphicon-repeat">&lt;/i>', ['grid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
        ],
        '{export}',
        //'{toggleData}',
        //'toggleDataContainer' => ['class' => 'btn-group-sm'],
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

echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);
