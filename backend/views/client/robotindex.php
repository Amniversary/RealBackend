<?php

\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'蜜播 ID'
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'用户昵称'
    ],
    [
       'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'create_robot_no',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'创建房间时直播间机器人人数',
        'editableOptions'=>function($model)
        {
            return [
                'name' => 'create_robot_no',
                'value' => $model['create_robot_no'],
                'formOptions'=>['action'=>'/client/robot_no?user_id='.strval($model['client_id'])],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'audience_robot_no',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'每个观众进入直播间时带来的机器人数量',
        'editableOptions'=>function($model)
        {
            return [
                'name' => 'audience_robot_no',
                'value' => $model['audience_robot_no'],
                'formOptions'=>['action'=>'/client/robot_no?user_id='.strval($model['client_id'])],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true
    ],

];


echo GridView::widget([
    'id'=>'cover_list',
    'dataProvider' => $dataProvider,

    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[
        [
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar'=> [
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
    'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]],
    'panel' => [
        'type' => GridView::TYPE_INFO
    ],

]);

$js='
';
$this->registerJs($js,\yii\web\View::POS_END);





