<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/13
 * Time: 16:13
 */
\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    [
        'attribute'=>'record_id',
        'vAlign'=>'middle',
        'label'=>'记录ID',
        'width'=>'200px',
    ],
    [
        'attribute'=>'living_id',
        'vAlign'=>'middle',
        'label'=>'直播间ID',
        'width'=>'200px',
    ],
    [
        'attribute'=>'room_no',
        'vAlign'=>'middle',
        'label'=>'房间号',
        'width'=>'200px',
    ],
    [
        'attribute'=>'living_master_id',
        'vAlign'=>'middle',
        'label'=>'主播ID',
        'width'=>'200px',
    ],
    [
        'attribute'=>'user_id',
        'vAlign'=>'middle',
        'label'=>'竞猜人ID',
        'width'=>'200px',
    ],
    [
        'attribute'=>'is_ok',
        'vAlign'=>'middle',
        'label'=>'是否竞猜成功',
        'width'=>'200px',
        'value'=>function($model)
        {
            if($model['is_ok'] == 1 ){
                return '是';
            }else  {
                return '否';
            }
        },
        'filter'=>['0'=>'否','1'=>'是'],
    ],
    [
        'attribute'=>'living_type',
        'vAlign'=>'middle',
        'label'=>'直播类型',
        'width'=>'200px',
        'value'=>function($model)
        {
            switch($model['living_type'])
            {
                case 3:
                    $living_type = '私密直播';
                    break;
                case 4:
                    $living_type = '门票直播';
                    break;
                case 5:
                    $living_type = '假直播';
                    break;
            }
            return $living_type;

        },
        'filter'=>['3'=>'私密直播','4'=>'门票直播','5'=>'假直播'],
    ],
    [
        'attribute'=>'guess_type',
        'vAlign'=>'middle',
        'label'=>'竞猜类型',
        'width'=>'200px',
        'value'=>function($model)
        {
            switch($model['guess_type'])
            {
                case 1:
                    $guess_type = '竞猜进入';
                    break;
                case 2:
                    $guess_type = '门票进入';
                    break;
            }
            return $guess_type;

        },
        'filter'=>['1'=>'竞猜进入','2'=>'门票进入']
    ],
    [
        'attribute'=>'guess_money',
        'vAlign'=>'middle',
        'label'=>'竞猜金额',
        'width'=>'200px',
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'label'=>'创建时间',
        'width'=>'200px',
    ]
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



$js = '
';
$this->registerJs($js,\yii\web\View::POS_END);
?>
