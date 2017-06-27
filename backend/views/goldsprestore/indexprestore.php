<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 13:55
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;


$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'prestore_id',
        'vAlign'=>'middle',
        'label'=>'充值ID',
    ],
    [
        'attribute'=>'user_id',
        'vAlign'=>'middle',
        'label'=>'用户ID',
    ],
    [
        'attribute'=>'gold_goods_id',
        'vAlign'=>'middle',
        'label'=>'金币商品ID',
    ],
    [
        'attribute'=>'gold_goods_name',
        'vAlign'=>'middle',
        'label'=>'金币商品名称',
        'width'=>'120px',
    ],
    [
        'attribute'=>'gold_goods_num',
        'vAlign'=>'middle',
        'label'=>'金币商品数量',
    ],
    [
        'attribute'=>'pay_money',
        'vAlign'=>'middle',
        'width'=>'200px',
        'label'=>'支付金额',
    ],
    [

        'attribute'=>'status_result',
        'vAlign'=>'middle',
        'label'=>'支付状态',
        'value'=>function($model)
        {
            return \backend\models\UserGoldsPrestoreForm::GetPrestoreStatus($model['status_result']);
        },
        'filter'=>['0'=>'支付失败','1'=>'支付中','2'=>'支付成功'],
    ],
    [
        'attribute'=>'pay_type',
        'vAlign'=>'middle',
        'label'=>'支付类型',
        'value'=>function($model)
        {
            return \backend\models\UserGoldsPrestoreForm::GetPrestorePayStatus($model['pay_type']);
        },
        'filter'=>['3'=>'支付宝支付','4'=>'微信支付','5'=>'连连支付','6'=>'苹果支付','100'=>'Web微信支付'],
    ],
    [
        'attribute'=>'create_time',
        'label'=>'创建时间',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'width'=>'250px',
        'template'=>'{update}',
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index){
            $url = '';
            switch($action) {
                case 'update':
                    $url = '/goldsprestore/detail?prestore_id='.strval($model['prestore_id']);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['data-toggle'=>'modal', 'data-target'=>'#contact-modal'],
        'buttons'=>[
            'update'=>function($url,$model)
            {
                if($model['status_result'] == 1)
                {
                    return Html::a('检验',$url,[ 'data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px;']);
                }
                return '';
            }
        ],
    ],

];
echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

echo GridView::widget([
    'id'=>'recharge_list',
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
$(function(){
$("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
});
';
$this->registerJs($js,\yii\web\View::POS_END);