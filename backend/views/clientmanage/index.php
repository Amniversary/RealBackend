<style>
    .user-pic
    {
        width: 60px;
    }
    .check-item
    {
        margin-right: 10px;
    }
    .form-control.my-input
    {
        display: inline;
        width: auto;
    }
</style>
<?php
\common\assets\ArtDialogAsset::register($this);
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:38
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;


$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'label'=>'状态',
        'class' => 'kartik\grid\EditableColumn',
        'width'=>'100px',
        'attribute'=>'status',
        'value'=>function($model)
        {
            return \backend\models\ClientSearchModel::GetStatusName($model['status']);
        },
        'filter'=>['0'=>'禁用','1'=>'正常'],
        'headerOptions'=>['class'=>'kv-sticky-column'],
        'contentOptions'=>['class'=>'kv-sticky-column'],
        'editableOptions'=>function($model,$key,$index)
        {
            return [
                'name'=>'AccountInfo['.$index.'][status]',
                'formOptions'=>['action'=>'/clientmanage/setstatus?account_id='.strval($model['account_id'])],
                'header'=>'状态',
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
               // 'inputContainerOptions'=>['name'=>'status'],
                'displayValueConfig'=>['0'=>'禁用','1'=>'正常'],
                'data'=>['0'=>'禁用','1'=>'正常'],
                'value'=>$model['status']
            ];
        },
        'readonly'=>function($model)
        {
            return $model->status === 2? true:false;
        },
        'refreshGrid'=>true,
    ],
    [
        'label'=>'内部',
        'class' => 'kartik\grid\EditableColumn',
        'attribute'=>'is_inner',
        'value'=>function($model)
        {
            return \backend\models\ClientSearchModel::GetInnerName($model['is_inner']);
        },
        'filter'=>['1'=>'否','2'=>'是'],
        'headerOptions'=>['class'=>'kv-sticky-column'],
        'contentOptions'=>['class'=>'kv-sticky-column'],
        'editableOptions'=>function($model,$key,$index)
        {
            return [
                'name'=>'AccountInfo['.$index.'][is_inner]',
                'formOptions'=>['action'=>'/clientmanage/setinner?account_id='.strval($model['account_id'])],
                'header'=>'状态',
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'options'=>['selected'=>$model['is_inner']],
                'displayValueConfig'=>['1'=>'否','2'=>'是'],
                'data'=>['1'=>'否','2'=>'是'],
                'value'=>$model['is_inner']
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'label'=>'用户id',
        'attribute'=>'account_id',
    ],
    [
        'label'=>'用户昵称',
        'attribute'=>'nick_name',
    ],
    [
        'label'=>'账户余额',
        'attribute'=>'balance',
    ],
    [
        'label'=>'手机号码',
        'attribute'=>'phone_no',
    ],
    [
        'label'=>'认证等级',
        'attribute'=>'centification_level',
        'value'=>function($model)
        {
            return \backend\models\ClientSearchModel::GetLevelName($model['centification_level']);
        },
    ],
    [
        'label'=>'创建时间',
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'width'=>'100px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{modify_money}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'modify_money':
                    $url = '/clientmanage/modify_balance?account_id='.strval($model['account_id']);
                    break;
            }
            return $url;
        },
        'buttons'=>[
            'modify_money'=>function($url,$model)
            {
                return Html::a('修改余额','#',['class'=>'balance-modify','data-url'=>"$url",'data-toggle'=>"modal",'data-target'=>"#multi-modal"]);
            },
        ],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'id'=>'user-manage-list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:500px;'], // only set when $responsive = false
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
    'toolbar' =>  [
        ['content'=>'',
           // Html::button('新增热词', ['type'=>'button', 'title'=>'新增用户', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('hotwords/create').'";return false;']),// . ' '.
            //Html::a('&lt;i class="glyphicon glyphicon-repeat">&lt;/i>', ['grid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
        ],
        //'{export}',
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
    'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]],
    //'floatHeader' => true,
    //'floatHeaderOptions' => ['scrollingTop' => '300px'],
    //'showPageSummary' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);

\yii\bootstrap\Modal::begin([
        'id' => 'multi-modal',
        'clientOptions' => false,
        'header' => Html::button('修改余额',['class' => 'btn btn-default','id'=>'set_finance']).' '.Html::button('取消',['aria-hidden'=>'true', 'class' => 'btn btn-default','data-dismiss'=>'modal']),
        'size'=>\yii\bootstrap\Modal::SIZE_SMALL,
    ]
);
echo Html::beginTag('div',['class'=>'mulitremark']);
echo Html::radio('OperateType',true,['id'=>'add_money','value'=>'4']).Html::label('增加金额','add_money',['class'=>'check-item']).Html::radio('OperateType',false,['id'=>'sub_money','value'=>'5']).Html::label('扣除金额','sub_money',['class'=>'check-item']);
echo '<br/>';
echo '<br/>';
echo Html::label('金额','input_remark',['class'=>'check-item']);
echo Html::input('text','input_money',null,['class'=>'form-control my-input','id'=>'input_money']);
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
var curUrl = null;
$(".balance-modify").on("click",function(){
    curUrl = $(this).attr("data-url");
    $("#multi-modal").modal("show");
return false;
});

$("#set_finance").on("click",function(){
    isSub = $("#IsSubmit").val();
    if(isSub == "1")
    {
        return;
    }
    op = $("input[type=\"radio\"]:checked").val();
    money = $("#input_money").val();
    money = money.replace(/(^\s*)|(\s*$)/g, "");
    if(money.length == 0 || isNaN(money))
    {
        artDialog.tips("金额必须是数字");
        return;
    }
    $("#IsSubmit").val("1");
    dataStr = "operate_type="+ op+"&op_money="+money;
            $.ajax({
        type: "POST",
        url: curUrl,
        data:dataStr,
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                     $("#user-manage-list").yiiGridView("applyFilter");
                     $("#multi-modal").modal("hide");
                 }
                 else
                 {
                     artDialog.tips(data.msg,2);
                 }
                 $("#IsSubmit").val("0");
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                artDialog.tips("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                $("#IsSubmit").val("0");
             }
        });
});
';
$this->registerJs($js,\yii\web\View::POS_END);