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
    [
        'attribute'=>'device_type',
        'vAlign'=>'middle',
        'label' => '设备类型',
        'value'=>function($model)
        {
            return ($model->device_type == 1? 'android' : 'ios');
        },
        'filter'=>['1'=>'Android','2'=>'IOS'],
    ],
     [
         'attribute'=>'gift_name',
         'vAlign'=>'middle',
         'label' => '礼物名称',
         'width'=>'135px',
     ],
    [
        'attribute'=>'gift_type',
        'vAlign'=>'middle',
        'label' => '类型',
        'value' => function($model)
        {
            return $model['gift_type']==1?'实际':'虚拟';
        }
    ],
    [
        'attribute'=>'total_gift_value',
        'vAlign'=>'middle',
        'label' => '实际豆值',
    ],
    [
        'attribute'=>'multiple',
        'vAlign'=>'middle',
        'label' => '倍数',
    ],
    [
        'attribute'=>'receive_rate',
        'vAlign'=>'middle',
        'label' => '转换率',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label' => '收礼物者蜜播ID',
        'width'=>'135px',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label' => '收礼物者昵称',
    ],
    [
        'attribute'=>'before_balance',
        'vAlign'=>'middle',
        'label' => '操作前余额',
    ],
    [
        'attribute'=>'after_balance',
        'vAlign'=>'middle',
        'label' => '操作后金额',
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'label' => '操作时间',
        'width'=>'320px',
    ],
     /*[
         'attribute'=>'operate_type',
         'vAlign'=>'middle',
         'label' => '操作类型',
         'value'=>function($model)
         {
             $type = '';
             switch($model->operate_type){
                 case 1:
                     $type = '充值';
                     break;
                 case 3:
                     $type = '票转豆';
                     break;
                 case 6:
                     $type = '送礼物';
                     break;
                 case 12:
                     $type = '发送弹幕';
                     break;
                 case 15:
                     $type = '后台修改增加豆';
                     break;
                 case 17:
                     $type = '发红包';
                     break;
                 case 18:
                     $type = '收红包';
                     break;
                 case 19:
                     $type = '退红包';
                     break;
                 case 20:
                     $type = '后台修改减少豆';
                     break;
             }
             return $type;
         },
         'filter'=>['1'=>'充值','3'=>'票转豆','6'=>'送礼物','12'=>'发送弹幕','15'=>'后台修改增加豆','17'=>'发红包','18'=>'收红包','19'=>'退红包','20'=>'后台修改减少豆'],
     ],*/

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
               // 'content'=> Html::button('返回',['type'=>'button','title'=>'返回', 'class'=>'btn btn-success return_client']),
            ],
            //'{export}',
            //'{toggleData}',
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
$(document).on("click",".return_client",function(){
   $("#contact-modal").modal("hide");
})
';
$this->registerJs($js,\yii\web\View::POS_END);