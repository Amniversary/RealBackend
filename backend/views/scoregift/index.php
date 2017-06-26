<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 15:33
 */

\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;
use frontend\business\ActivityUtil;

$gridColumns = [
    [
        'attribute'=>'activity_id',
        'vAlign'=>'middle',
        'width'=>'60px',
        'label'=>'活动 ID'
    ],
    [
        'attribute'=>'title',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'活动标题'
    ],
    [
        'attribute'=>'start_time',
        'vAlign'=>'middle',
        'width'=>'150px',
        'label'=>'开始时间',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'attribute'=>'end_time',
        'vAlign'=>'middle',
        'width'=>'150px',
        'label'=>'结束时间',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'attribute'=>'activity_status',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'活动状态',
        'value' => function($model)
        {
            switch($model['activity_status']){
                case '0':
                    $model['activity_status'] = "已结束";
                    break;
                case '1':
                    $model['activity_status'] = "进行中";
                    break;
                case '2':
                    $model['activity_status'] = "未开始";
                    break;
            }
            return $model['activity_status'];
        },
        'filter'=>['0'=>'已结束','1'=>'已开始','2'=>'未开始']
    ],
    [
        'attribute'=>'template_id',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'模板类型',
        'filter'=> \backend\business\ScoreGiftUtil::GetActivityTemplate(),
        'value'=>function($model)
        {
            switch($model['template_id'])
            {
                case $model['template_id']:
                    $model['template_id'] = $model['template_title'];
                    break;
            }
            return $model['template_id'];
        }
    ],
    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{detail}{update}&nbsp;&nbsp;{delete}&nbsp;&nbsp;&nbsp;&nbsp;',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            $page = \yii::$app->request->get('page');
            switch($action)
            {
                case 'update':
                    $url = '/scoregift/update?page='.$page.'&activity_id='.strval($model['activity_id']);
                    break;
                case 'delete':
                    $url = '/scoregift/delete?activity_id='.strval($model['activity_id']);
                    break;
                case 'detail':
                    $url = ActivityUtil::GetActivityUrl($model['activity_id']);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'update' => function ($url, $model, $key)
            {
                if($model['activity_status'] != 0){
                    return Html::a('编辑',$url,[ 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
                }
            },
            'delete' =>function ($url, $model,$key)
            {
                if($model['activity_status'] == 2) {
                    return Html::a('删除', $url, ['title' => '删除', 'class' => 'delete', 'data-toggle' => false, 'data-confirm' => '确定要删除该记录吗？', 'data-method' => 'post', 'data-pjax' => '1', 'style' => 'margin-left:10px;color:red']);
                }
            },
            'detail' => function($url, $model, $key){
                return Html::a(
                    '积分榜详情',$url,
                    //['update', 'id' => $key],
                    ['class'=>'detail','target'=>'_blank']
                );
            }
        ],
    ],

];


echo GridView::widget([
    'id'=>'params_list',
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
            'content'=> Html::button('新增活动',['type'=>'button','title'=>'新增活动', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('scoregift/create').'";return false;']),

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

$js='
//   将获得的积分排行榜页面在新窗口展示
     $(".detail").click(function(){
        var href = $(this).attr("href");
        window.open(href);
        return false;
     })
';
$this->registerJs($js,\yii\web\View::POS_END);




