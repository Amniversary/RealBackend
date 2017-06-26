<style>
    .ctr-head
    {
        margin-bottom: 10px;
    }
    .mulitremark
    {
        vertical-align: middle;
    }
    .labelremark
    {
        vertical-align: middle;
        display: block;
    }
    .inputremark
    {
        width: 100%;
        height: 150px;
        resize: none;
    }
    .wd
    {
        width: 40px;
        display: block;
        text-align: center;
        border-radius: 5px;
        margin: auto;
    }
</style>
<?php
use kartik\grid\GridView;
use yii\bootstrap\Html;
use \yii\bootstrap\Tabs;

echo Tabs::widget([
    'options'=>['class'=>'ctr-head'],
    'items' => [
        [
            'label' => '未打款提现记录',
            'url' => \Yii::$app->urlManager->createAbsoluteUrl(['getcash/index','data_type'=>'undo']),// $this->render('indexundo'),
            'active' => ($data_type === 'undo'? true: false),
            'options' => ['id' => 'my_get_cash_undo'],
        ],
        [
            'label' => '已打款提现记录',
            'url' =>\Yii::$app->urlManager->createAbsoluteUrl(['getcash/indexhis','data_type'=>'his']),//$this->render('indexhis'),
            'headerOptions' => [],
            'options' => ['id' => 'my_get_cash_his'],
            'active' => ($data_type === 'his'? true: false)
        ],
    ],
]);

\common\assets\ArtDialogAsset::register($this);

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'class'=>'\yii\grid\CheckboxColumn',
        'checkboxOptions'=>function($model)
        {
            return [
                'value'=>$model['get_cash_id'],
            ];
        },
        'name'=>'get_cash_id',
    ],
    [
        'label'=>'提现金额',
        'attribute'=>'cash_money'
    ],
    [
        'label'=>'账户余额',
        'attribute'=>'balance'
    ],
    [
        'label'=>'审核状态',
        'width'=>'100px',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
                return \backend\models\GetCashForm::GetStatusName($model['status']);
        },
        'filter'=>false,
    ],
    [
        'label'=>'首次提现',
        'attribute'=>'first_get_money',
        'vAlign'=>'middle',
        'width'=> '100px',
        'format' => 'html',
        'value'=>function($model)
        {
            if($model['first_get_money'] == 1)
            {
                return "<span class=\"bg-green wd\">✓</span>";
            }
            return '';
            /*return $model->first_get_money;*/
        },
        'filter'=>['1'=>'首次'],

    ],
    [
        'label'=>'身份证',
        'attribute'=>'identity_no',
    ],
    [
        'label'=>'姓名',
        'attribute'=>'real_name',
    ],
    [
        'label'=>'银行卡号',
        'attribute'=>'card_no',
    ],
    [
        'label'=>'银行名称',
        'attribute'=>'bank_name',
    ],
    [
        //'class'=>'kartik\grid\BooleanColumn',
        'label'=>'审核时间',
        'attribute'=>'check_time',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            //'attribute'=>'start_time',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'width'=>'120px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{finance}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'finance':
                    $url='/getcash/financeshow?get_cash_id='.$model['get_cash_id'];
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],//tooltip
        'buttons'=>[
            'finance' => function ($url, $model, $key) {
                return Html::a('打款',$url,[ 'data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px;']);
            },
        ],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'id'=>'get_cash_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'autoXlFormat'=>true,
    'containerOptions' => ['style'=>'overflow: auto;height:620px;'], // only set when $responsive = false
    'beforeHeader'=>[
        [
//            'columns'=>[
//                ['content'=>'Header Before 1', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 2', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 3', 'options'=>['colspan'=>3, 'class'=>'text-center warning']],
//            ],
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    //\Yii::$app->urlManager->createUrl('wishrecommend/create'),
    'toolbar' =>  [
        ['content'=>Html::button('批量设置打款',['class' => 'btn btn-default','data-toggle'=>'modal', 'data-target'=>'#multi-modal']),
        ],
        '{export}',
        '{toggleData}',
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']
    ],
    'pjax' => true,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    //'floatHeader' => true,
    //'floatHeaderOptions' => ['scrollingTop' => '300px'],
    //'showPageSummary' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);
//data-dismiss="modal" aria-hidden="true"
echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

\yii\bootstrap\Modal::begin([
        'id' => 'multi-modal',
        'clientOptions' => false,
        'header' => Html::button('设置打款',['class' => 'btn btn-default','id'=>'set_finance']).' '.Html::button('取消',['aria-hidden'=>'true', 'class' => 'btn btn-default','data-dismiss'=>'modal']),
        'size'=>\yii\bootstrap\Modal::SIZE_SMALL,
    ]
);
echo Html::beginTag('div',['class'=>'mulitremark']);
echo Html::label('备注','input_remark',['class'=>'labelremark']);
echo Html::textarea('remark','',['class'=>'inputremark','id'=>'input_remark']);
echo Html::endTag('div');
\yii\bootstrap\Modal::end();
echo Html::hiddenInput('IsSubmit','0',['id'=>'IsSubmit']);
$js='
artDialog.tips = function (content, time) {
    return artDialog({
        id: "Tips",
        title: false,
        cancel: false,
        fixed: true,
        lock: true
    })
    .content("<div style=\"padding: 0 1em;\">" + content + "</div>")
    .time(time || 1);
};
var dialog = null;
$(document).on("click","#set_finance",function(){
    var keys = $("#get_cash_list").yiiGridView("getSelectedRows");
    length = keys.length;
    if(length <= 0)
    {
        artDialog.tips("未选择打款记录");
        return;
    }
    issubmit = $("#IsSubmit").val();
    if(issubmit == "1")
    {
        return;
    }
    $("#IsSubmit").val("1");
    data="";
    for(i=0; i < length; i++)
    {
        data += "GetCashId[]=" +keys[i].toString()+"&";
    }
    remark = $("#input_remark").val();
    data += "remark="+remark;
    dialog = art.dialog({
        title:"请选择上传图片",
        fixed:true,
        lock:true,
        content:"<img src=\"http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/loading24.gif\" />",
        ok:false,
        cancel:false,
    });
    $.ajax({
        type: "POST",
        url: "/getcash/mulitfinance",
        data: data,
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                    if(dialog != null)
                     {
                        dialog.close();
                     }
                     artDialog.tips(data.msg);
                     $("#IsSubmit").val("0");
                     $("#get_cash_list").yiiGridView("applyFilter");
                    $("#multi-modal").modal("hide");
                 }
                 else
                 {
                     $("#IsSubmit").val("0");
                     artDialog.tips(data.msg);
                     if(dialog != null)
                     {
                        dialog.close();
                     }
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                 if(dialog != null)
                 {
                    dialog.close();
                 }
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                $("#IsSubmit").val("0");
             }
        });
});
';
$this->registerJs($js);
