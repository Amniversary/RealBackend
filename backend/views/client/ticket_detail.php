<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/17
 * Time: 13:10
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
//    [
//        'attribute'=>'user_id',
//        'vAlign'=>'middle',
//        'label' => '用户ID',
//        'width'=>'70px',
//    ],
//    [
//        'attribute'=>'balance_id',
//        'vAlign'=>'middle',
//        'label' => '用户账户ID',
//        'width'=>'100px',
//    ],
     [
         'attribute'=>'device_type',
         'vAlign'=>'middle',
         'label' => '设备类型',
         'width'=>'80px',
         'value'=>function($model)
         {
             return ($model['device_type'] == 1? 'android' : 'ios');
         },
         'filter'=>['1'=>'Android','2'=>'IOS'],
     ],
     [
         'attribute'=>'operate_type',
         'vAlign'=>'middle',
         'label' => '操作类型',
         'value'=>function($model)
         {
             $type = '';
             switch($model['operate_type']){
                 case 2:
                     $type = '票转豆';
                     break;
                 case 4:
                     $type = '票提现';
                     break;
                 case 7:
                     $type = '收礼物';
                     break;
                 case 13:
                     $type = '退款';
                     break;
                 case 29:
                     $type = '收门票，票增加';
                     break;
                 case 30:
                     $type = '可提现票数增加';
                     break;
                 case 31:
                     $type = '可提现票数减少';
                     break;
             }
             return $type;
         },
         'filter'=>['2'=>'票转豆','4'=>'票提现','7'=>'收礼物','13'=>'退款','29'=>'收门票，票增加','30'=>'可提现票数增加','31'=>'可提现票数减少'],
     ],
    [
        'attribute'=>'cash_type',
        'vAlign'=>'middle',
        'label' => '操作方式',
        'value' => function($model)
        {
            $type = '';
            if($model['operate_type'] == 1)
            {
                switch($model['cash_type']){
                    case 3:
                        $type = '支付宝';
                        break;
                    case 4:
                        $type = '微信支付';
                        break;
                    case 6:
                        $type = '苹果支付';
                        break;
                }
            }
            return $type;
        },
        'filter'=>['3'=>'支付宝','4'=>'微信支付','6'=>'苹果支付'],
    ],
    [
        'attribute'=>'before_balance',
        'vAlign'=>'middle',
        'label' => '操作前余额'
    ],
     [
         'attribute'=>'operate_value',
         'vAlign'=>'middle',
         'label' => '交易数'
     ],

    [
        'attribute'=>'after_balance',
        'vAlign'=>'middle',
        'label' => '操作后金额'
    ],
    [
        'attribute'=>'account_balance',
        'vAlign'=>'middle',
        'label' => '操作用户'
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'label' => '操作时间',
        'width'=>'320px',
    ],
//    [
//            'attribute'=>'remark1',
//            'vAlign'=>'middle',
//            'label' => '操作字段'
//    ],

];
echo \yii\bootstrap\Alert::widget([
    'body'=>'搜索的日期格式：yyyy-mm-dd hh:mm:ss|yyyy-mm-dd hh:mm:ss，时间间隔请不要超过15天',
    'options'=>[
        'class'=>'alert-warning',
    ]
]);
?>
    <style>
        .alert-warning{
            background-color: #f39c12 !important;
            color: #fff !important;
            padding: 15px;
        }

    </style>
<?php
echo GridView::widget([
    'id'=>'goods_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:480px;font-size:14px;'],
    'beforeHeader'=>[
        [
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar'=> [
            [
                //'content'=> Html::button('返回',['type'=>'button','title'=>'返回', 'class'=>'btn btn-success return_client']),
            ],
            //'{export}',
            //'{toggleData}',
            /*'toggleDataContainer' => ['class' => 'btn-group-sm'],
            'exportContainer' => ['class' => 'btn-group-sm']*/

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
$("body").on("click",".return_client",function(){
   $("#contact-modal").modal("hide");

})

';
$this->registerJs($js,\yii\web\View::POS_END);