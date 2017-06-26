<style type="text/css">
    .kv-editable-input {
        height: 80px;
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

$livingTypes = [
    1 => '普通直播',
    2 => '游戏直播',
    3 => '密码直播',
    4 => '门票直播',
    5 => '假直播'
];
$gridColumns = [
    [
        'attribute'=>'code',
        'vAlign'=>'middle',
        'options'=>[
            'style' => 'width:120px'
        ]
    ],
    [
        'attribute'=>'title',
        'vAlign'=>'middle',
        'options'=>[
            'style' => 'width:150px'
        ]
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'value1',
        'vAlign'=>'middle',
        'editableOptions' => function($model) use($livingTypes)
        {
            return [
                'formOptions'=>[
                    'action'=>'/updatemanage/set_value1?params_id='.strval($model->params_id),

                ],
                'size' => 'md',
                'inputType' => \kartik\editable\Editable::INPUT_CHECKBOX_LIST,
                'data' => $livingTypes,
            ];
        },
        'value' => function($model) use($livingTypes) {
            !is_array($model->value1) && $model->value1 = json_decode($model->value1, true);
            $value = $model->value1;
            if (empty($value)) {
                $model->value1 = null;
                return null;
            }
            $types = [];
            foreach ($value as $i) {
                isset($livingTypes[$i]) && $types[] = $livingTypes[$i];
            }
            return implode('，', $types);
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
