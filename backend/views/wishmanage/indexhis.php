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
        'attribute'=>'wish_id',
        'label'=> '愿望id',
        'width' => '100px',
    ],
    [
        'attribute'=>'wish_name',
        'label' =>'愿望名称',
        'vAlign'=>'middle',
        'format'=>'raw',
        'value'=>function($model)
        {
            $shareInfo = null;
            $error = '';
            if(!\frontend\business\ShareUtil::GetShareInfoForWish($model['wish_id'], $shareInfo,$error))
            {
                return $model['wish_name'];
            }
            else
            {
                $url =$shareInfo['link'];
                $fontDomain = \Yii::$app->params['FrontDomain'];
                $domain = $_SERVER['HTTP_HOST'];
                $url = str_replace($domain,$fontDomain,$url);
                return Html::a($model['wish_name'],$url,['target'=>'_blank']);
            }

        }
    ],
    [
        'attribute'=>'nick_name',
        'label'=>'用户昵称',
    ],
    [
        'attribute'=>'user_name',
        'label'=>'用户姓名',
    ],
    [
        'attribute'=>'phone_no',
        'label' => '手机号码',
    ],
    [
        'label'=>'实现',
        'attribute'=>'is_finish',
        'filter' =>['1'=>'未实现','2'=>'已实现'],
        'value'=>function($model)
        {
            return $res = \common\models\Wish::GetIsFinish($model['is_finish']);
        }
    ],
    [
        'attribute'=>'wish_money',
        'label' =>'愿望金额',
    ],
    [
        'label'=>'已筹金额',
        'value'=>function($model)
        {
            return $model['ready_reward_money'] + $model['red_packets_money'];
        },
        'width'=>'100px',
    ],
    [
        'attribute'=>'ready_reward_money',
        'label' => '打赏金额',
        'width'=>'100px',
    ],
    [
        'attribute'=>'red_packets_money',
        'label' =>'红包金额',
        'width'=>'100px',
    ],
    [
        'attribute'=>'view_num',
        'label' =>'浏览数量',
        'width'=>'100px',
    ],
    [
        'attribute'=>'reward_num',
        'label' =>'打赏次数',
        'width'=>'100px',
    ],
    [
        'attribute'=>'comment_num',
        'label' =>'评论数',
        'width'=>'100px',
    ],
    [
        'attribute'=>'collect_num',
        'label' =>'收藏数量',
        'width'=>'100px',
    ],
    [
        //'class'=>'kartik\grid\BooleanColumn',
        'attribute'=>'create_time',
        'label' =>'创建时间',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            //'attribute'=>'start_time',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'label'=>'状态',
        'value'=>function($model)
        {
            return $res = \common\models\Wish::GetFinishStatus($model);
        },
        'width'=>'100px',
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:620px;'], // only set when $responsive = false
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
       // '{toggleData}',
       // 'toggleDataContainer' => ['class' => 'btn-group-sm'],
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
