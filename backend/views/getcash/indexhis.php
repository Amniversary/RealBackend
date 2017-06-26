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
            'label' => '未打款提现记录',
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['getcash/index','data_type'=>'undo']),// $this->render('indexundo'),
            'active' => ($data_type === 'undo'? true: false),
            'options' => ['id' => 'my_get_cash_undo'],
        ],
        [
            'label' => '已打款提现记录',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['getcash/indexhis','data_type'=>'his']),//$this->render('indexhis'),
            'headerOptions' => [],
            'options' => ['id' => 'my_get_cash_his'],
            'active' => ($data_type === 'his'? true: false)
        ],
    ],
]);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'label'=>'提现金额',
        'attribute'=>'cash_money'
    ],
    [
        'label'=>'账户余额',
        'attribute'=>'balance'
    ],
    [
        'label'=>'审核状态',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'value'=>function($model)
        {

            return \backend\models\GetCashForm::GetStatusName($model['status']);
        },
        'filter'=>['3'=>'已打款','4'=>'审核拒绝'],
    ],
    [
        'label'=>'身份证',
        'attribute'=>'identity_no',
    ],
    [
        'label'=>'姓名',
        'attribute'=>'real_name',
    ],
    [
        'label'=>'银行卡号',
        'attribute'=>'card_no',
    ],
    [
        'label'=>'银行名称',
        'attribute'=>'bank_name',
    ],
    [
        //'class'=>'kartik\grid\BooleanColumn',
        'label'=>'审核时间',
        'attribute'=>'check_time',
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
    'autoXlFormat'=>true,
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

echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);
