<style>
    .user-pic
    {
        width: 60px;
    }
    .check-item
    {
        margin-right: 10px;
    }
    .form-control.my-input
    {
        display: inline;
        width: auto;
    }
</style>

<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 10:32
 */
\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;
// echo $dataProvider;exit;
$gridColumns = [
   [
        'attribute'=>'remark4',
        'vAlign'=>'middle',
        'label'=>'被操作蜜播ID',
        // 'width'=>'200px',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'被操作蜜播呢称',
        // 'width'=>'300px',
    ],
    [
         'attribute'=>'operate_type',
         'vAlign'=>'middle',
         'label'=>'操作类型',
         'value'=>function($model){
             if ( $model['operate_type'] == 1 ) {
                 return 'App端';
             }else{
                 return '系统后台';
             }
         },
         'filter'=>['1'=>'App端','2'=>'系统后台'],

    ],
    [
        'attribute'=>'manage_id',
        'vAlign'=>'middle',
        'label'=>'操作员用户ID',
    ],
    [
        'attribute'=>'manage_name',
        'vAlign'=>'middle',
        'label'=>'操作员用户名',
    ],
    [
         'attribute'=>'manage_type',
         'vAlign'=>'middle',
         'label'=>'操作内容',
         'value'=>function($model){
             if ( $model['manage_type'] == 1 ) {
                 return '禁播';
             }else{
                 return '解禁';
             }
         },
         'filter'=>['1'=>'禁播','0'=>'解禁'],
    ],
    [
        'attribute'=>'remark1',
        'vAlign'=>'middle',
        'label'=>'操作原因',
        'width'=>'300px',
    ],
    [
        'attribute'=>'create_date',
        'vAlign'=>'middle',
        'label'=>'操作时间',
        'width'=>'200px',
    ],
];

echo GridView::widget([
    'id'=>'living_list',
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


$this->registerJs($js,\yii\web\View::POS_END);
?>