<style>
    .user-pic
    {
        width: 150px;
        /*border-radius: 50%;*/
    }
    .kv-panel-before .pull-right
    {
        float: left !important;
    }
</style>
<?php
use kartik\grid\GridView;
use yii\bootstrap\Html;


$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'wish_recommend_id',
        'label'=>'推荐号',
    ],
    [
        'attribute'=>'wish_name',
        'label'=>'愿望名称',
    ],
    [
        'attribute'=>'nick_name',
        'label'=>'发布人',
    ],
    [
        'attribute'=>'phone_no',
        'label'=>'手机号',
    ],
    [
        'label'=>'排序号',
        'class' => 'kartik\grid\EditableColumn',
        'width'=>'100px',
        'attribute'=>'order_no',
        //'filter'=>['0'=>'禁用','1'=>'正常'],
        'headerOptions'=>['class'=>'kv-sticky-column'],
        'contentOptions'=>['class'=>'kv-sticky-column'],
        'editableOptions'=>function($model,$key,$index)
        {
            return [
                'formOptions'=>['action'=>'/wishrecommend/setorderno?recommend_id='.strval($model['wish_recommend_id'])],
                'header'=>'排序号',
                'size'=>'md',
                'name'=>'WishRecommend['.$index.'][order_no]',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                'value'=>$model['order_no'],
               // 'displayValueConfig'=>['0'=>'禁止','1'=>'正常'],
               // 'data'=>['0'=>'禁止','1'=>'正常'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        //'class'=>'kartik\grid\BooleanColumn',
        'label'=>'发布时间',
        'attribute'=>'create_time',
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
        'width'=>'250px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'delete':
                    $url = '/wishrecommend/delete?recommend_id='.strval($model['wish_recommend_id']);
                    break;
            }
            return $url;
        },
        'deleteOptions'=>['title'=>false,'label'=>'删除','data-confirm'=>'确定要删除该记录吗？', 'data-toggle'=>false,'data-pjax'=>'1'],
        'buttons'=>[
/*            'update'=>function($url,$model)
            {
                return Html::a('编辑',$url);
            },*/
        ],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'id'=>'wish_recommend_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:500px;'], // only set when $responsive = false
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
        ['content'=>
        Html::a('新增推荐',\Yii::$app->urlManager->createUrl('wishrecommend/create'),['class' => 'btn btn-default','data-toggle'=>'modal', 'data-target'=>'#contact-modal']),
            //Html::button('新增推荐', ['type'=>'button', 'title'=>'新增推荐', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('carouselmanage/create').'";return false;']),// . ' '.
            //Html::a('&lt;i class="glyphicon glyphicon-repeat">&lt;/i>', ['grid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
        ],
        //'{export}',
        //'{toggleData}',
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm'],
    ],
    'pjax' => true,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]],
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