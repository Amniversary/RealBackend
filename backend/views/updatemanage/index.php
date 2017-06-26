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
    [
        'attribute'=>'code',
        'vAlign'=>'middle',
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'title',
        'vAlign'=>'middle',
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/updatemanage/set_system_title?params_id='.strval($model->params_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'description',
        'vAlign'=>'middle',
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/updatemanage/set_description?params_id='.strval($model->params_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'value1',
        'vAlign'=>'middle',
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/updatemanage/set_value1?params_id='.strval($model->params_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'value2',
        'vAlign'=>'middle',
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/updatemanage/set_value2?params_id='.strval($model->params_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'value3',
        'vAlign'=>'middle',
        'width'=>'300px',
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/updatemanage/set_value3?params_id='.strval($model->params_id)],
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,

            ];
        },
        'refreshGrid'=>true,
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
    'toolbar'=> [
        [
            'content'=> Html::button('新增系统参数',['type'=>'button','title'=>'新增系统参数', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('updatemanage/create').'";return false;']),

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
    'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]],
    'panel' => [
        'type' => GridView::TYPE_INFO
    ],

]);
