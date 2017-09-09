<style>
    .back-a {
        display: inline-block;
        font-size: 14px;
        border-radius: 3px;
        color: #00a7d0;
        border: 1px solid #00a7d0;
        padding: 3px 5px;
    }

    .back-btn {
        display: inline-block;
        font-size: 14px;
        margin-bottom: 0px;
        border-radius: 3px;
        color: #00a7d0;
        border: 1px solid #00a7d0;
        padding: 3px 5px;
    }

    .tag-label {
        margin-left: 10px;
    }
</style>
<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;

common\assets\ArtDialogAsset::register($this);

$gridColumns = [
    [
        'attribute' => 'record_id',
        'vAlign' => 'middle',
        'label' => '#',
        'filter' => false
    ],
    [
        'attribute' => 'nick_name',
        'vAlign' => 'middle',
        'format' => 'html',
        'label' => '公众号名称',
        'width' => '150px',
        'value' => function ($model) {
            $url = empty($model['head_img']) ? 'http://oss.aliyuncs.com/meiyuan/wish_type/default.png' : $model['head_img'];
            return Html::img($url, ['class' => 'user-pic', 'style' => 'width:40px', 'value' => $model['nick_name']]) . '&nbsp' . Html::label($model['nick_name']);
        },
        'filter' => true,
    ],
    [
        'label' => '公众号类型',
        'attribute' => 'service_type_info',
        'vAlign' => 'middle',
        'width' => '100px',
        'value' => function ($model) {
            return \common\models\AuthorizationList::getServiceTypeInfo($model['service_type_info']);
        },
        'filter' => false,
    ],
    [
        'label' => '认证类型',
        'attribute' => 'verify_type_info',
        'vAlign' => 'middle',
        'value' => function ($model) {
            return \common\models\AuthorizationList::getVerifyTypeInfo($model['verify_type_info']);
        },
        'filter' => false,
    ],
    [
        'label' => '净增人数',
        'attribute' => 'net_user',
        'vAlign' => 'middle',
    ],
    [
        'label' => '新增人数',
        'attribute' => 'new_user',
        'vAlign' => 'middle',
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'label' => '总粉丝数',
        'attribute' => 'count_user',
        'vAlign' => 'middle',
        'width' => '100px',

        'editableOptions' => function ($model) {
            return [
                'formOptions' => ['action' => '/publiclist/set_count?app_id=' . strval($model['record_id'])],
                'size' => 'min',
                'value' => $model['count_user'],
                'name' => 'count_user',
                'inputType' => \kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid' => true,
    ],
    [
        'width' => '270px',
        'class' => 'kartik\grid\ActionColumn',
        'template' => '{wxbackend}{set_alarm}',
        'dropdown' => false,
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            $url = '';
            switch ($action) {
                case 'wxbackend':
                    $url = '/publiclist/status?record_id=' . $model['record_id'];
                    break;
                case 'set_alarm':
                    $url = '/publiclist/set_alarm?record_id=' . $model['record_id'];
                    break;
            }
            return $url;
        },
        'buttons' => [
            'wxbackend' => function ($url, $model, $key) {
                $userId = Yii::$app->user->id;
                $cacheInfo = Yii::$app->cache->get('app_backend_' . $userId);
                $cacheInfo = json_decode($cacheInfo, true);
                if (!empty($cacheInfo && $model['record_id'] == $cacheInfo['record_id'])) {
                    return Html::label('已选择', '#', ['class' => 'back-btn', 'style'=>'margin-right:2%']);
                } else {
                    return Html::a('选择管理后台', $url, ['class' => 'back-a select', 'data-toggle' => false, 'style'=>'margin-right:2%']);
                }
            },
            'set_alarm' => function($url, $model, $key) {
                if($model['alarm_status'] == 1) {
                    return Html::a('告警状态:开', $url, ['class'=>'back-a alarm-on', 'data-toggle' => false,]);
                } else {
                    return Html::a('告警状态:关', $url, ['class'=> 'back-a alarm-off', 'data-toggle' => false,'style'=>'color:red; border:1px solid red;']);
                }
            }
        ],
    ]
];

echo GridView::widget([
    'id' => 'public_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader' => [['options' => ['class' => 'skip-export']]],
    'toolbar' => [
        [
            'content' => Html::a('选择标签', Yii::$app->urlManager->createUrl('publiclist/get_tag_list'), ['type' => 'button', 'class' => 'btn btn-success', 'title' => '选择标签', 'data-toggle' => 'modal', 'data-target' => '#contact-modal']) .
                Html::button('添加公众号', ['type' => 'button', 'title' => '添加公众号', 'class' => 'btn btn-success', 'onclick' => 'location="' . \Yii::$app->urlManager->createUrl('publiclist/create') . '";return false;']),
        ],
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']
    ],
    //'pjax' => true,
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


echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size' => \yii\bootstrap\Modal::SIZE_DEFAULT,
    ]
);
if (empty($data)) $data = [];
foreach ($data as $item) {
    $js .= '$(document).ready(function(){
       $(".pull-left div").append("<label class=\"back-btn tag-label\">' . $item . '</label>");
});';
}

$js .= '
$(document).on("click", ".alarm-on", function() {
    $url = $(this).attr("href") + "&status=1";
    var dialog = art.dialog({
        title: "修改告警状态 ...",
        fixed:true,
        lock:true,
    })
    $.ajax({
        type:"POST",
        url: $url,
        data :"",
        success: function(data) {
            data = $.parseJSON(data);
            if(data.code == 0) {
                $("#public_list").yiiGridView("applyFilter");
            } else {
                alert("修改失败：" + data.msg);
            }
            if(dialog != null) dialog.close();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
            if(dialog != null) dialog.close();
        }
    });
    return false;
});
$(document).on("click", ".alarm-off", function() {
    $url = $(this).attr("href") + "&status=2";
    var dialog = art.dialog({
        title: "修改告警状态 ...",
        fixed:true,
        lock:true,
    })
    $.ajax({
        type:"POST",
        url: $url,
        data :"",
        success: function(data) {
            data = $.parseJSON(data);
            if(data.code == 0) {
                $("#public_list").yiiGridView("applyFilter");
            } else {
                alert("修改失败：" + data.msg);
            }
            if(dialog != null) dialog.close();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
            if(dialog != null) dialog.close();
        }
    });
    return false;
});
$(".select").on("click",function(){
    $url = $(this).attr("href");
    $.ajax({
        type: "POST",
        url: $url,
        data: "",
        success: function(data) {
            data = $.parseJSON(data);
            if(data.code == "0") {
                $("#public_list").yiiGridView("applyFilter");
            } else {
                alert("选择失败：" + data.msg);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
        }
    });
    return false;
});
';
$this->registerJs($js, \yii\web\View::POS_END);