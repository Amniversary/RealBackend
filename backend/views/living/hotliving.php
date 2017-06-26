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
        'width'=>'99px',
    ],
    [
        'attribute'=>'living_title',
        'vAlign'=>'middle',
        'label'=>'直播标题',
        'width'=>'200px',
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'s1',
        'vAlign'=>'middle',
        'label'=>'用户状态',
        'width'=>'150px',
        'value'=>function($model)
        {
            return \backend\models\ClientHotLivingForm::GetStatus($model['s1']);
        },
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'s1',
                'formOptions'=>['action'=>'/living/set_status?client_id='.strval($model['client_id'])],
                'size'=>'md',
                'value'=>$model['s1'],
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['0'=>'禁止','1'=>'正常'],
            ];
        },
        'refreshGrid'=>true,
        'filter'=>['0'=>'禁止','1'=>'正常'],
    ],
    [
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label'=>'直播状态',
        'format'=>'raw',
        'width'=>'109px',
        'value' => function($model)
        {
            $url = '/living/look_living?living_id='.strval($model['living_id']);
            if($model['status'] == 2){
                if($model['living_type'] == 2)
                {
                    return Html::a(Html::img('http://mbpic.mblive.cn/meibo-test/lock.png',['class'=>'user-pic','style'=>'width:35px;hieght:35px','title'=>$url]),$url,['data-toggle'=>'modal','class'=>'living_title','data-target'=>'#contact-modal','data-id'=>$model['living_id']]);
                }
                return Html::a(\backend\models\ClientHotLivingForm::GetLivingStatus($model['status']),$url,['data-toggle'=>'modal','class'=>'living_title','data-target'=>'#contact-modal','data-id'=>$model['living_id']]);
            }
            else
            {
                return \backend\models\ClientHotLivingForm::GetLivingStatus($model['status']);
            }
        },
        'filter'=>['0'=>'结束','1'=>'暂停','2'=>'直播','3'=>'禁用'],
    ],
    [
        'attribute'=>'living_type',
        'vAlign'=>'middle',
        'label'=>'直播类型',
        'value'=>function($model)
        {
            if($model['status'] == 2) {
                switch($model['living_type']){
                    case 1:
                        $type = '普通直播';
                        break;
                    case 3:
                        $type = '私密直播';
                        break;
                    case 4:
                        $type = '门票直播';
                        break;
                };
                return $type;
            }
            else
            {
                return '';
            }
        },
        'width'=>'109px',
        'filter'=>['1'=>'正常直播','3'=>'私密直播','4'=>'门票直播'],
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'limit_num',
        'vAlign'=>'middle',
        'label'=>'限制人数数量',
        'width'=>'80px',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'limit_num',
                'value'=>$model['limit_num'],
                'formOptions'=>['action'=>'/living/set_limit_num?living_id='.strval($model['living_id']).'&is_contract='.strval($model['is_contract'])],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'device_type',
        'vAlign'=>'middle',
        'label'=>'设备',
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
                'value'=> $model['order_no'],
                'formOptions'=>['action'=>'/living/set_order?living_id='.strval($model['living_id'])],
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
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'living_num',
        'vAlign'=>'middle',
        'label'=>'场次',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'living_num',
                'value'=>$model['living_num'],
                'formOptions'=>['action'=>'/living/living_hot?living_id='.strval($model['living_id'])],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
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
        'attribute'=>'is_contract',
        'vAlign'=>'middle',
        'label'=>'是否签约',
        'width'=>'150px',
        'value'=>function($model)
        {
            return (($model['is_contract'] == 1)? '否':'是');
        },
        'filter'=>['1'=>'否','2'=>'是'],
    ],
    [
        'attribute'=>'is_official',
        'vAlign'=>'middle',
        'label'=>'官方',
        'width'=>'80px',
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

$this->registerJsFile('http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/jwplayer.js');
$js='
$("#living_list-pjax").on("click",".living_title",function(){
    $url = $(this).attr("href");
    $dataid = $(this).attr("data-id");
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
                                sources: [
                                    {
                                        "file":data.msg.pull_rtmp_url
                                    },
                                    {
                                        "file":data.msg.pull_http_url
                                    },
                                    {
                                        "file":data.msg.pull_hls_url
                                    }
                                ]

                            });
                            thePlayer.onPlaylistItem(function(){//开始播放一个视频时,但总觉得这个方法不稳定
                                $(".jw-display-icon-container").hide();                               
                            });
                        },
                        content:[ 
                            "<a class=\"btn btn-success\" style=\"margin-bottom: 10px;\"  href=javascript:closelive("+$dataid+") >关闭直播</a><div id=\"living_stat\"></div>"
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

function closelive(id){
    $.ajax({
        type: "POST",
        url: "/living/closelive?living_id="+id,
        data: "",
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == 0)
                {
                     $(".aui_state_focus").remove();
                     $("#living_list").yiiGridView("applyFilter");
                 }
                 else
                 {
                    $(".aui_state_focus").remove();
                     alert("关闭直播失败：" + data.msg);
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
             }
        });
}



';

$this->registerJs($js,\yii\web\View::POS_END);