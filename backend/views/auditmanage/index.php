<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:38
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'create_user_name',
        'vAlign'=>'middle',
    ],
    [
        'label'=>'内部人员',
        'attribute'=>'create_user_id',
        'value'=>function($model)
        {
            $user = \frontend\business\PersonalUserUtil::GetAccontInfoById($model->create_user_id);
            return ((isset($user)&&($user->is_inner === 2))?'是':'否');
        }
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
        'attribute'=>'business_type',
        'value'=>function($model)
        {
            return $model->GetCheckTypeName();
        },
        'filter'=>\common\models\BusinessCheck::GetCheckTypeDropdownListData(),
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
    'check_no',
    [
        'width'=>'120px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{check}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { return 'check?relate_id='.$model->relate_id.'&check_id='.$model->business_check_id; },
        'updateOptions'=>['title'=>'审核','label'=>'审核', 'data-toggle'=>false],//tooltip
        'buttons'=>[
            'check' => function ($url, $model, $key) {
                return Html::a('审核',$url,[ 'data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px;']);
            },
        ],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'id'=>'gd_check_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;'], // only set when $responsive = false
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

$js = '
$(function(){
$("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
});
';
$this->registerJs($js,\yii\web\View::POS_END);