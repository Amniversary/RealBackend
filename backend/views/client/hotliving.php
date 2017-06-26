<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 10:32
 */
\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;


$gridColumns = [
    [
        'attribute'=>'living_id',
        'vAlign'=>'middle',
        'label'=>'直播 ID',
        'width'=>'200px',
    ],
    [
        'attribute'=>'living_title',
        'vAlign'=>'middle',
        'label'=>'直播标题',
        'width'=>'300px',
    ],
    [
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label'=>'状态',
        'format'=>'raw',
        'value' => function($model)
        {
            $url = '/client/look_living?living_id='.strval($model['living_id']);
            if($model['status'] == 2){
                return Html::a(\backend\models\ClientHotLivingForm::GetLivingStatus($model['status']),$url,['data-toggle'=>'modal','class'=>'living_title','data-target'=>'#contact-modal']);
            }
            else
            {
                return \backend\models\ClientHotLivingForm::GetLivingStatus($model['status']);
            }
        },
        'filter'=>['0'=>'结束','1'=>'暂停','2'=>'直播','3'=>'禁用'],

    ],
    [
        'attribute'=>'device_type',
        'vAlign'=>'middle',
        'label'=>'设备类型',
        'value'=>function($model)
        {
            return \backend\models\ClientHotLivingForm::GetDeviceType($model['device_type']);
        }
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'order_no',
        'vAlign'=>'middle',
        'label'=>'排序号',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'order_no',
                'formOptions'=>['action'=>'/client/set_order?living_id='.strval($model['living_id'])],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'hot_num',
        'vAlign'=>'middle',
        'label'=>'热度',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label'=>'蜜播 ID',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'用户昵称',
    ],
    [
        'attribute'=>'city',
        'vAlign'=>'middle',
        'label'=>'城市',
        'width'=>'150px',
    ],
    [
        'attribute'=>'is_official',
        'vAlign'=>'middle',
        'label'=>'官方',
        'value'=>function($model)
        {
            return (($model['is_official'] == 0)? '否':'是');
        }
    ],
];

echo GridView::widget([
    'id'=>'living_list',
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

//(new \yii\web\View())
$this->registerJsFile('http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/jwplayer.js');
$js='
$("#living_list-pjax").on("click",".living_title",function(){
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
                    var d = art.dialog({
                        title:"观看直播",
                        init:function(){
                            var playerInstance = jwplayer("living_stat");
                            var thePlayer= playerInstance.setup({
                                flashplayer: "http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/jwplayer.flash.swf",
                                width: "600px",
                                height:"400px",
                                aspectratio: "16:9",
                                autostart:true,//自动播放
                                "sources": [
                                    {
                                        "file":data.msg.pull_http_url
                                    },
                                    {
                                        "file":data.msg.pull_rtmp_url
                                    },
                                    {
                                        "file":data.msg.pull_hls_url
                                    }
                                ]

                            });
                            thePlayer.onPlay(function(){
                                $(".jwplayer").removeClass("jw-state-buffering").addClass("jw-state-playing");
                            })
                        },
                        content:[
                            "<div id=\"living_stat\"></div>"
                        ].join(),

                    });
                }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
            }
    });
});


$("#living_delete").on("click",function(){
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