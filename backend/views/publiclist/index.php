<style>
    .back-a{
        display: inline-block;
        font-size: 14px;
        border-radius: 3px;
        color: #00a65a;
        border:1px solid #00a65a;
        padding: 6px 12px;
    }
</style>
<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class'=>'yii\grid\SerialColumn'],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'format'=>'html',
        'label'=>'公众号名称',
        'width'=>'150px',
        'value'=>function($model)
        {
            $url = empty($model->head_img)?'http://oss.aliyuncs.com/meiyuan/wish_type/default.png':$model->head_img;
            return Html::img($url,['class'=>'user-pic','style'=>'width:40px','value'=>$model->nick_name]).'&nbsp'. Html::label($model->nick_name);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'service_type_info',
        'vAlign'=>'middle',
        'width'=>'100px',
        'value'=>function($model){
            return $model->getServiceTypeInfo($model->service_type_info);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'verify_type_info',
        'vAlign'=>'middle',
        'value'=>function($model){
            return $model->verify_type_info == '-1' ? '未认证':'已认证';
        },
        'filter'=>false,
    ],
    [
        'width'=>'300px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{wxbackend}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'wxbackend':
                    $url = '/usermanage/update?user_id';
                    break;
            }
            return $url;
        },
        'buttons'=>[
            'wxbackend' => function ($url, $model, $key) {
                return Html::a('进入管理后台',$url,['class'=>'back-a','data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px;']);
            },

        ],
    ]
];

echo GridView::widget([
    'id'=>'public_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('添加公众号',['type'=>'button','title'=>'添加公众号', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('publiclist/create').'";return false;']),
        ],
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']
    ],
    'pjax' => true,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    //'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]],
    'panel' => [
        'type' => GridView::TYPE_INFO
    ],
]);