<style>
    .ctr-head
    {
        margin-bottom: 10px;
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
use \yii\bootstrap\Tabs;

echo Tabs::widget([
    'options'=>['class'=>'ctr-head'],
    'items' => [
        [
            'label' => '愿望评论',
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['commentmanage/index','data_type'=>'undo']),// $this->render('indexundo'),
            'active' => ($data_type === 'undo'? true: false),
            'options' => ['id' => 'my_comment_undo'],
        ],
        [
            'label' => '打赏评论',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['commentmanage/indexhis','data_type'=>'his']),//$this->render('indexhis'),
            'headerOptions' => [],
            'options' => ['id' => 'my_comment_his'],
            'active' => ($data_type === 'his'? true: false)
        ],
    ],
]);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'label'=>'评论id',
        'attribute'=>'wish_comment_id',
        'width'=>'100px',
    ],
    [
        'label'=>'愿望名称',
        'attribute'=>'wish_name',
        'width'=>'100px',
    ],
    [
        'label'=>'评论人',
        'attribute'=>'talk_user',
        'width'=>'100px',
    ],
    [
        'label'=>'评论内容',
        'attribute'=>'content',
        'width'=>'100px',
    ],
    [
        'label'=>'评论时间',
        'attribute'=>'create_time',
        'width'=>'120px',
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
        'width'=>'100px',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return \common\models\WishComment::GetStatusName($model['status']);
        },
        'filter'=>['1'=>'正常','0'=>'禁用'],
    ],
    [
        'attribute'=>'remark2',
        'label'=>'禁用理由',
        'width'=>'200px',
    ],
    [
        'attribute'=>'remark4',
        'label'=>'禁用人',
        'width'=>'100px',
    ],
    [
        'width'=>'120px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{forbidcomment}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'forbidcomment':
                    $url='/commentmanage/forbidcomment?comment_id='.$model['wish_comment_id'];
                    break;
            }
            return $url;
        },
        'buttons'=>[
            'forbidcomment' => function ($url, $model, $key) {
                if($model['status'] === '0')
                {
                    return '';
                }
                return Html::a('禁止',$url,[ 'data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px;']);
            },
        ],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'id'=>'wish_comment_list',
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
        //'{export}',
        //'{toggleData}',
        //'toggleDataContainer' => ['class' => 'btn-group-sm'],
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
