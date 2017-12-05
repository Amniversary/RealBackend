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

$gridColumns = [
    [
        'attribute' => 'id',
        'vAlign' => 'middle',
        'label' => 'ID',
        'filter' => true,
        'width' => '8%',
    ],
    [
        'attribute' => 'user_id',
        'vAlign' => 'middle',
        'label' => '用户 ID',
        'filter' => true,
        'width' => '8%'
    ],
    [
        'attribute' => 'nick_name',
        'vAlign' => 'middle',
        'label' => '昵称',
    ],
    [
        'attribute' => 'name',
        'vAlign' => 'middle',
        'label' => '真实姓名',
    ],
    [
        'attribute' => 'money',
        'vAlign' => 'middle',
        'label' => '提现金额',
        'value' => function ($model) {
            return $model['money'] . '元';
        }
    ],
    [
        'attribute' => 'result_money',
        'vAlign' => 'middle',
        'label' => '实际提现金额',
        'value' => function ($model) {
            return $model['result_money'] . '元';
        }
    ],
    [
        'attribute' => 'status',
        'vAlign' => 'middle',
        'label' => '审核状态',
        'value' => function ($model) {
            switch ($model['status']) {
                case '1': $str = '未审核'; break;
                case '2': $str = '已打款'; break;
                case '0': $str = '打款失败'; break;
                case '3': $str = '打款中'; break;
                default : $str = '未知类型'; break;
            }
            return $str;
        },
        'filter' => ['1' => '未审核', '2' => '已打款', '0' => '打款失败', '3'=> '打款中'],
    ],
    [
        'attribute' => 'cash_rate',
        'vAlign' => 'middle',
        'label' => '提现费率',
        'value' => function ($model) {
            return sprintf('%.02f', $model['cash_rate'] * 10) . '%';
        }
    ],
    [
        'attribute' => 'create_at',
        'vAlign' => 'middle',
        'label' => '提现时间',
        'value' => function ($model) {
            return date('Y-m-d H:i:s', $model['create_at']);
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'template' => '{select}',
        'dropdown' => false,
        'vAlign' => 'middle',
        'width' => '150px',
        'urlCreator' => function ($action, $model, $key, $index) {
            $url = '';
            switch ($action) {
                case 'select':
                    $url = '/cashaudit/select?id=' . strval($model['id']);
                    break;
            }
            return $url;
        },
        'viewOptions' => ['title' => '查看', 'data-toggle' => 'tooltip'],
        'updateOptions' => ['title' => '编辑', 'label' => '编辑', 'data-toggle' => false],
        'deleteOptions' => ['title' => '删除', 'label' => '删除', 'data-toggle' => false],
        'buttons' => [
            'select' => function ($url, $model) {
                return Html::a('打款', $url, ['style' => 'margin-right:10px', 'class' => 'back-a', 'data-toggle' => 'modal','data-target' => '#contact-modal']);
            },
        ]
    ]
];
echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size' => \yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

echo GridView::widget([
    'id' => 'cash_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader' => [['options' => ['class' => 'skip-export']]],
    'toolbar' => [
        [
            'content' => ''
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

$js = '
$("#cash_list-pjax").on("click",".delete",function(){
    if(!confirm("确定要删除该记录吗？")) {
        return false;
    }
    $url = $(this).attr("href");
        $.ajax({
        type: "POST",
        url: $url,
        data: "",
        success: function(data) {
            data = $.parseJSON(data);
            if(data.code == "0") {
                $("#cash_list").yiiGridView("applyFilter");
            } else {
                alert("删除失败：" + data.msg);
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