<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 11:10
 */

\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;
use frontend\business\ActivityUtil;

$gridColumns = [
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'width'=>'60px',
        'label'=>'蜜播 ID'
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'蜜播昵称'
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'anchor_salary',
        'vAlign'=>'middle',
        'width'=>'100px',
        'label'=>'主播薪资',
        'editableOptions'=>function($model)
        {
            return [
                'name' => 'anchor_salary',
                'value' => $model['anchor_salary'],
                'formOptions'=>['action'=>'/signanchor/set_salary?user_id='.strval($model['user_id'])],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        }
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'anchor_time',
        'vAlign'=>'middle',
        'width'=>'150px',
        'label'=>'签约日期',
        'editableOptions'=>function($model)
        {
            return [
                'name' => 'anchor_time',
                'value' => $model['anchor_time'],
                'formOptions'=>['action'=>'/signanchor/set_time?user_id='.strval($model['user_id'])],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
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
        'template'=>'{detail}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'detail':
                    $url = 'http://phpproject.com/mibo/backend_anchor_log/anchor_log.html?salary_id='.$model['salary_id'];
                    break;
            }
            return $url;
        },
        'buttons'=>[
            'detail' => function($url, $model, $key){
                return Html::a(
                    '日志明细',$url,
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




