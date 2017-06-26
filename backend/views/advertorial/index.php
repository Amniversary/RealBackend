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
        'attribute'=>'advertorial_title',
        'vAlign'=>'middle',
        'width'=>'60px',
        'label'=>'软文标题'
    ],
//    [
//        'attribute'=>'advertorial_content',
//        'vAlign'=>'middle',
//        'width'=>'100px',
//        'label'=>'软文内容',
//        'format' => 'html',
//    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'width'=>'150px',
        'label'=>'发布时间',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{preview}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/advertorial/update?record_id='.strval($model['record_id']);
                    break;
                case 'delete':
                    $url = '/advertorial/delete?record_id='.strval($model['record_id']);
                    break;
                case 'preview':
                    $url = 'http://'.$_SERVER['HTTP_HOST'].'/advertorial/detail?record_id='.strval($model['record_id']);
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
            'delete' =>function ($url, $model,$key) {

               return Html::a('删除', $url, ['title' => '删除', 'class' => 'delete', 'data-toggle' => false, 'data-confirm' => '确定要删除该记录吗？', 'data-method' => 'post', 'data-pjax' => '1']);

            },
            'preview' => function ($url, $model, $key)
            {
                return Html::a('预览',$url,[ 'data-target'=>'#contact-modal','style'=>'margin-left:10px','class'=>'detail','target'=>'_blank']);
            },
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
            'content'=> Html::button('新增软文',['type'=>'button','title'=>'新增活动', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('advertorial/create').'";return false;']),

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




