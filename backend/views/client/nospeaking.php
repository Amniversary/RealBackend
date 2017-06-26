<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/17
 * Time: 13:10
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
\common\assets\ArtDialogAsset::register($this);
$gridColumns = [
    [
        'attribute'=>'client_id',
        'vAlign'=>'middle',
        'width'=>'250px',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'width'=>'250px',
    ],
    [
        'attribute'=>'status',
        'vAlign'=>'middle',
        'width'=>'250px',
        'value' => function($model)
        {
            return $model->GetUserStatus();
        },
        'filter'=>['1'=>'正常','0'=>'禁用'],
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
    ],
    [
        'width'=>'150px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update_client_id}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'buttons'=>[
            'update_client_id'=>function($url,$model)
            {
                return Html::a('查询禁言','#',['data-id'=>"$model->client_id",'data-toggle'=>"modal",'data-target'=>"#multi-modal"]);
            },
        ],
    ]
];

echo GridView::widget([
    'id'=>'goods_list',
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

\yii\bootstrap\Modal::begin([
        'id' => 'multi-modal',
        'clientOptions' => false,
        'header' => '<h4 class="modal-title">禁言设置</h4>',
        'footer' => Html::button('确定',[
                'class'             => 'btn btn-primary',
                'id'                => 'set_nospeaking',
                'data-loading-text' => 'Loading...',
                'autocomplete'      => 'off',
            ]).Html::button('取消',[
                'aria-hidden'=>'true',
                'class' => 'btn btn-default',
                'data-dismiss'=>'modal'
            ]),
        'size'   => \yii\bootstrap\Modal::SIZE_DEFAULT,
    ]
);
echo Html::beginTag('form',['class'=>'form-horizontal', 'id' => 'modal_form']);
echo Html::beginTag('div',['class'=>'form-group']);
echo '<p class="col-sm-offset-3 text-warning">0 代表不禁言，4294967295 代表永久禁言</p>';
echo Html::endTag('div');
echo Html::beginTag('div',['class'=>'form-group']);
echo Html::label('单聊消息禁言时间', 'C2Cmsg_nospeaking_time', ['class'=>'col-sm-3 control-label']);
echo Html::beginTag('div',['class'=>'col-sm-9']);
echo Html::input('text','C2Cmsg_nospeaking_time',null,['class'=>'form-control','id'=>'C2Cmsg_nospeaking_time']);
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::beginTag('div',['class'=>'form-group']);
echo Html::label('群聊消息禁言时间', 'groupmsg_nospeaking_time', ['class'=>'col-sm-3 control-label']);
echo Html::beginTag('div',['class'=>'col-sm-9']);
echo Html::input('text','groupmsg_nospeaking_time',null,['class'=>'form-control','id'=>'groupmsg_nospeaking_time']);
echo Html::endTag('div');
echo Html::endTag('div');
echo Html::hiddenInput('client_id', null, ['id' => 'client_id']);
echo Html::endTag('form');
\yii\bootstrap\Modal::end();

$js = <<<EOF
var modal = $('#multi-modal'),
    GETNOSPEAKINGURL = '/client/get_nospeaking',
    SETNOSPEAKINGURL = '/client/set_nospeaking';
var modalErrorAlert = function(msg) {
    artDialog({
        content: msg,
        okValue: '确定',
        ok: function() {
        }
    });
};
//ajax请求出错时回调
var modalFail = function(error) {
    modalErrorAlert(error.responseText);
}
//ajax请求成功时回调
var modalSuccessFormFill = function(result) {
    if (typeof result != 'object') {
        result = $.parseJSON(result);
    }
    if (!parseInt(result.code)) {
        $('#C2Cmsg_nospeaking_time').val(result.data.C2CmsgNospeakingTime);
        $('#groupmsg_nospeaking_time').val(result.data.GroupmsgNospeakingTime);
    } else {
        modalErrorAlert(result.message);
    }
}

//获取禁言信息
modal.on('show.bs.modal', function(e) {
    var relatedTarget = $(e.relatedTarget);
    var id = relatedTarget.data('id');
    $('#client_id').val(id);
    $.getJSON(GETNOSPEAKINGURL, {client_id: id})
    .done([modalSuccessFormFill])
    .fail([modalFail]);
});

//保存禁言时间修改
modal.find('#set_nospeaking').on('click.nospeaking', function() {
    var btn = $(this);
    btn.button('loading');
    $.post(SETNOSPEAKINGURL, $('#modal_form').serializeArray())
    .done([modalSuccessFormFill])
    .fail([modalFail])
    .always(function() {
        btn.button('reset');
    });
});
EOF;

$this->registerJs($js,\yii\web\View::POS_END);