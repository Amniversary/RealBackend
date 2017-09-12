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
        color: #00a65a;
        border: 1px solid #00a65a;
        padding: 3px 3px;
    }
</style>
<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;

common\assets\ArtDialogAsset::register($this);
/**
 * @var $model common\models\BatchCustomer
 */


$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'task_name',
        'vAlign' => 'middle',
        'filter' => false,
    ],
    [
        'attribute' => 'status',
        'vAlign' => 'middle',
        'value' => function ($model) {
            switch ($model->status) {
                case 1:$rst = '未执行';break;
                case 2:$rst = '队列中';break;
                case 0:$rst = '执行成功';break;
                default:$rst = '未知类型';break;
            }
            return $rst;
        },
        'filter' => ['1'=>'未执行', '2'=>'队列中', '3'=>'执行中', '0'=>'已完成'],
    ],
    [
        'attribute' => 'create_time',
        'vAlign' => 'middle',
        'filter' => false,
        'value' => function ($model) {
            return empty($model->create_time) ? '' : date('Y-m-d H:i:s', $model->create_time);
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'template' => '{get_auth}{send}{create}{delete}{status}',
        'dropdown' => false,
        'vAlign' => 'middle',
        'width' => '350px',
        'urlCreator' => function ($action, $model, $key, $index) {
            $url = '';
            switch ($action) {
                case 'get_auth':
                    $url = '/template/get_auth?id=' . strval($model->id);
                    break;
                case 'create':
                    $url = '/template/index_msg?id=' . strval($model->id);
                    break;
                case 'delete':
                    $url = '/template/delete_params?id=' . strval($model->id);
                    break;
                case 'send':
                    $url = '/template/start_task?id=' . strval($model->id);
                    break;
                case 'status':
                    $url = '/template/status?id=' . strval($model->id);
                    break;
            }
            return $url;
        },
        'viewOptions' => ['title' => '查看', 'data-toggle' => 'tooltip'],
        'updateOptions' => ['title' => '编辑', 'label' => '编辑', 'data-toggle' => false],
        'deleteOptions' => ['title' => '删除', 'label' => '删除', 'data-toggle' => false],
        'buttons' => [
            'get_auth' => function ($url, $model) {
                return Html::a('公众号', $url, ['style' => 'margin-right:2%', 'class' => 'back-a', 'data-toggle' => 'modal', 'data-target' => '#contact-modal']);
            },
            'send' => function ($url, $model) {
                if($model->status > 1 || $model->status == 0) return '';
                return Html::a('开始', $url, ['style' => 'margin-right:2%', 'class' => 'back-a start']);
            },
            'create' => function ($url, $model) {
                return Html::a('设置消息', $url, ['style' => 'margin-right:2%', 'class' => 'back-a']);
            },
            'delete' => function ($url, $model) {
                if($model->status > 2) return '';
                return Html::a('删除', $url, ['class' => 'delete back-a', 'data-toggle' => false, 'data-method' => 'post', 'data-pjax' => '1', 'style' => 'margin-right:2%']);
            },
            'status' => function ($url, $model) {
                if($model->status > 0) return '';
                return Html::a('状态', $url, ['class' => 'back-a']);
            }
        ],
    ]
];

echo GridView::widget([
    'id' => 'template_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader' => [['options' => ['class' => 'skip-export']]],
    'toolbar' => [
        [
            'content' => Html::button('创建任务', ['type' => 'button', 'class' => 'btn btn-primary', 'onclick' => 'location="' . Yii::$app->urlManager->createUrl('template/create_params') . '"; return false;'])
        ],
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']
    ],
    'pjax' => true,
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

echo yii\bootstrap\Modal::widget([
    'id' => 'contact-modal',
    'clientOptions' => false,
    'size' => yii\bootstrap\Modal::SIZE_LARGE
]);

$js = '
artDialog.tips = function(content, time) {
       return artDialog({
            id:"Tips",
            icon:"succeed",
            title:false,
            fixed:true,
            lock:true
       })
       .content("<div style=\"padding: 0 1em;\">" + content + "</div>")
       .time(time || 1);
}
$(document).on("click", ".start", function(){
    if(!confirm("确定要开始任务吗？")) {
        return false;
    }
    var dialog = art.dialog({
        title: "任务开始中 ...",
        fixed:true,
        lock:true,
    })
    $url = $(this).attr("href");
    $.ajax({
        type:"POST",
        url: $url,
        data: "",
        success:function(data) {
            data = $.parseJSON(data);
            if(data.code == 0) {
                artDialog.tips("任务已经开始");
                $("#template_list").yiiGridView("applyFilter");
            } else {
                art.dialog.alert("任务开始失败：" + data.msg);
            }
            if(dialog != null)
                dialog.close();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            art.Dialog.tips("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
            if(dialog != null) dialog.close();
        }
    })
    return false;
});
$("#template_list-pjax").on("click",".delete",function(){
if(!confirm("确定要删除该记录吗？")) {
    return false;
}
$url = $(this).attr("href");
    $.ajax({
        type: "POST",
        url: $url,
        data: "",
        success: function(data)
        {
           data = $.parseJSON(data);
           if(data.code == 0) {
                $("#template_list").yiiGridView("applyFilter");
           } else {
                alert("删除失败：" + data.msg);
           }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown)
        {
            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
        }
    });
    return false;
});
';
$this->registerJs($js, \yii\web\View::POS_END);