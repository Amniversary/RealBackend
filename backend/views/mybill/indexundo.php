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
            'label' => '未还款账单记录',
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['mybill/index','data_type'=>'undo']),// $this->render('indexundo'),
            'active' => ($data_type === 'undo'? true: false),
            'options' => ['id' => 'my_bill_undo'],
        ],
        [
            'label' => '已还款账单记录',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['mybill/indexhis','data_type'=>'his']),//$this->render('indexhis'),
            'headerOptions' => [],
            'options' => ['id' => 'my_bill_his'],
            'active' => ($data_type === 'his'? true: false)
        ],
    ],
]);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'label'=>'借款单号',
        'attribute'=>'borrow_fund_id',
        'width'=>'100px',
    ],
    [
        'label'=>'用户昵称',
        'attribute'=>'user_id',
        'width'=>'100px',
        'value'=>function($model)
        {
            $user = \frontend\business\PersonalUserUtil::GetAccontInfoById($model->user_id);
            return (isset($user)?$user->nick_name:'未设置');
        }
    ],
    [
        'label'=>'手机号码',
        'attribute'=>'user_id',
        'width'=>'100px',
        'value'=>function($model)
        {
            $user = \frontend\business\PersonalUserUtil::GetAccontInfoById($model->user_id);
            return (isset($user)?$user->phone_no:'未设置');
        }
    ],
    [
        'width'=>'100px',
      'attribute'=>'back_fee',
        ],
    'source_fee',
    'borrow_fee',
    [
        'width'=>'80px',
        'attribute'=>'is_cur_stage',
        'vAlign'=>'middle',
        'value'=>function($model)
        {

            return $model->GetIsCurStageName();
        },
        'filter'=>['1'=>'是','0'=>'否'],
    ],
    [
        'width'=>'150px',
        'attribute'=>'back_date',
        'filter'=>['1'=>'距离还款1天','2'=>'距离还款2天','2#1'=>'距离还款2天内','-1'=>'已逾期1天','-2'=>'已逾期2天','-1#-2'=>'逾期2天内','-3'=>'已逾期3天','-1#-3'=>'逾期3天内','-4'=>'已逾期4天','-1#-4'=>'逾期4天内','-5'=>'已逾期5天','-1#-5'=>'逾期5天内','-6#all'=>'逾期大于5天','delay_all'=>'所有逾期'],
    ],
    [
        'attribute'=>'cur_stage',
        'vAlign'=>'middle',
        'value'=>function($model)
        {

            return (strval($model->cur_stage).'/'.strval($model->by_stages_count));
        },
    ],
    [
        'attribute'=>'is_delay',
        'vAlign'=>'middle',
        'width'=>'80px',
        'value'=>function($model)
        {

            return ((time() > strtotime($model->back_date))?'是':'否');
        },
    ],
    [
        'attribute'=>'breach_days',
        'vAlign'=>'middle',
        'width'=>'80px',
        'value'=>function($model)
        {
            $days = \common\components\TimeUtil::GetDays(date('Y-m-d'),$model->back_date);
            return ($days < 0 ? 0 : $days);
        },
    ],
    [
        'width'=>'120px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{finance}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'finance':
                    $url='/mybill/remarkbadshow?my_bill_id='.$model->bill_id;
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],//tooltip
        'buttons'=>[
            'finance' => function ($url, $model, $key) {
                return Html::a('坏账',$url,[ 'data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px;']);
            },
        ],
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
