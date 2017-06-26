<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/17
 * Time: 13:10
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
\common\assets\ArtDialogAsset::register($this);
$gridColumns = [
    [
        'attribute'=>'quality_id',
        'vAlign'=>'middle',
        'width'=>'80px',
        'label' =>'ID',
    ],
    [
        'attribute'=>'quality',
        'vAlign'=>'middle',
        'label' =>'标题',
    ],
    [
        'attribute'=>'fps',
        'vAlign'=>'middle',
        'label' =>'视频帧数',
    ],
//    [
//        'attribute'=>'profilelevel',
//        'vAlign'=>'middle',
//        'label' =>'编码耗能',
//    ],
    [
        'attribute'=>'video_bit_rate',
        'vAlign'=>'middle',
        'label' =>'平均编码码率',

    ],
    [
        'attribute'=>'width',
        'vAlign'=>'middle',
        'label'=>'视频宽度',
    ],
    [
        'attribute'=>'height',
        'vAlign'=>'middle',
        'label'=>'视频高度',
    ],
    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;{black}&nbsp;&nbsp;{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/qiniuliving/update?quality_id='.strval($model['quality_id']);
                    break;
                case 'delete':
                    $url = '/qiniuliving/delete?quality_id='.strval($model['quality_id']);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'update' => function ($url, $model, $key)
            {
                return Html::a('编辑',$url,[ 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
            },
            'delete' =>function ($url, $model,$key)
            {
                if($model['quality_id'] != 1)
                {
                    return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-method'=>'post', 'data-pjax'=>'1','style'=>'margin-left:10px']);
                }
            }
        ],
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
    'toolbar'=> [
        [
            'content'=> Html::button('新增参数',['type'=>'button','title'=>'新增参数', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('qiniuliving/create').'";return false;']),
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

