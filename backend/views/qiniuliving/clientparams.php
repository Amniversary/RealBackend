<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 15:44
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
//    [
//        'attribute'=>'relate_id',
//        'vAlign'=>'middle',
//        'label'=>'主键自增',
//    ],
    [
        'attribute'=>'user_id',
        'vAlign'=>'middle',
        'width'=>'300px',
        'label'=>'用户 ID',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label'=>'蜜播 ID',
    ],
    [
        'attribute'=>'parameters_more',
        'vAlign'=>'middle',
        'label'=>'参数详情',
    ],
//    [
//        'attribute'=>'quality_id',
//        'vAlign'=>'middle',
//        'value'=>function($model)
//        {
//            return \backend\models\QiniuClientParamsForm::GetLivingParams($model['quality_id']);
//        },
//        'label'=>'参数模型',
//    ],
//    [
//        'attribute'=>'fps',
//        'vAlign'=>'middle',
//        'label'=>'视频帧数',
//    ],
//    [
//        'attribute'=>'profilelevel',
//        'vAlign'=>'middle',
//        'label'=>'编码耗能',
//    ],
//    [
//        'attribute'=>'video_bit_rate',
//        'vAlign'=>'middle',
//        'label'=>'平均编码码率',
//    ],
//    [
//        'attribute'=>'width',
//        'vAlign'=>'middle',
//        'label'=>'视频宽度',
//    ],
//    [
//        'attribute'=>'height',
//        'vAlign'=>'middle',
//        'label'=>'视频高度',
//    ],
    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/qiniuliving/update_client?relate_id='.strval($model['relate_id']);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'buttons'=>[
            'update' => function ($url, $model, $key)
            {
                if($model['parameters_more'] != null){
                    return Html::a('编辑',$url,[ 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
                }
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
    /*'toolbar'=> [
        [
            'content'=> Html::button('新增用户参数',['type'=>'button','title'=>'新增用户参数', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('qiniuliving/create_client').'";return false;']),

        ],
        '{export}',
        '{toggleData}',
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']

    ],*/
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
$("#goods_delete").on("click",function(){
    $url = $(this).attr("href");
            $.ajax({
        type: "POST",
        url: $url,
        data: "",
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                     $("#user-manage-list").yiiGridView("applyFilter");
                }
                else
                {
                    alert("删除失败：" + data.msg);
                }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
            }
        });
});
';
$this->registerJs($js,\yii\web\View::POS_END);