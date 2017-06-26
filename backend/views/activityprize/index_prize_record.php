<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26
 * Time: 18:55
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label'=>'蜜播 ID',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'label'=>'昵称',
    ],
    [
        'attribute'=>'title',
        'vAlign'=>'middle',
        'label'=>'活动名称',
        'width'=>'100px',
    ],
    [
        'attribute'=>'reward_name',
        'vAlign'=>'middle',
        'label'=>'奖励名称',
        'width'=>'100px',
        'value' => function($model)
        {
            $rst = '';
            switch($model['reward_name']){
                case '1': $rst = '一等奖'; break;
                case '2': $rst = '二等奖'; break;
                case '3': $rst = '三等奖'; break;
                case '4': $rst = '四等奖'; break;
                case '5': $rst = '五等奖'; break;
                case '6': $rst = '六等奖'; break;
                case '7': $rst = '七等奖'; break;
                case '8': $rst = '八等奖'; break;
            }
            return $rst;
        },
        'filter'=>['1'=>'一等奖','2'=>'二等奖','3'=>'三等奖','4'=>'四等奖','5'=>'五等奖','6'=>'六等奖','7'=>'七等奖','8'=>'八等奖']
    ],
    [
        'attribute'=>'prize_name',
        'vAlign'=>'middle',
        'label'=>'奖品名称',
        'width'=>'220px',

    ],
    [
        'attribute'=>'prize_type',
        'vAlign'=>'middle',
        'label'=>'奖品类型',
        'width'=>'100px',
        'value'=>function($model)
        {
            $rst = '';
            switch($model['prize_type'])
            {
                case 1: $rst = '鲜花'; break;
                case 2: $rst = '经验值'; break;
                case 3: $rst = '礼包'; break;
                case 4: $rst = '实物'; break;
            }
            return $rst;
        },
        'filter'=>['1'=>'鲜花','2'=>'经验值','3'=>'礼包','4'=>'实物'],
    ],
    [
        'attribute'=>'is_winning',
        'vAlign'=>'middle',
        'label'=>'是否中奖',
        'width'=>'100px',
        'value'=>function($model)
        {
            return (($model['is_winning'] == 1) ? '是' : '否');
        },
        'filter'=>['1'=>'是','0'=>'否'],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute'=>'is_send',
        'vAlign'=>'middle',
        'label'=>'奖品是否发出',
        'width'=>'120px',
        'value'=>function($model)
        {
            return (($model['is_send'] == 1) ?  '已发出' : '未发出');
        },
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'is_send',
                'value'=>$model['is_send'],
                'formOptions'=>['action'=>'/activityprize/set_prize_record?record_id='.strval($model['record_id'])],
                'size'=>'sm',
                'data'=>['1'=>'已发出','0'=>'未发出'],
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
            ];
        },
        'filter'=>['1'=>'已发出','0'=>'未发出'],
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'is_direct_send',
        'vAlign'=>'middle',
        'label'=>'是否直接分发',
        'width'=>'120px',
        'value'=>function($model)
        {
            return (($model['is_direct_send'] == 1) ? '是' : '否');
        },
        'filter'=>['1'=>'是','0'=>'否'],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute'=>'prize_user_name',
        'vAlign'=>'middle',
        'label'=>'收奖人姓名',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'prize_user_name',
                'value'=>$model['prize_user_name'],
                'formOptions'=>['action'=>'/activityprize/set_prize_record?record_id='.strval($model['record_id'])],
                'size'=>'sm',
                //'data'=>['1'=>'已发出','2'=>'未发出'],
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute'=>'prize_user_site',
        'vAlign'=>'middle',
        'label'=>'收奖人地址',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'prize_user_site',
                'value'=>$model['prize_user_site'],
                'formOptions'=>['action'=>'/activityprize/set_prize_record?record_id='.strval($model['record_id'])],
                'size'=>'sm',
                //'data'=>['1'=>'已发出','2'=>'未发出'],
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute'=>'express_num',
        'vAlign'=>'middle',
        'label'=>'快递单号',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'express_num',
                'value'=>$model['express_num'],
                'formOptions'=>['action'=>'/activityprize/set_prize_record?record_id='.strval($model['record_id'])],
                'size'=>'sm',
                //'data'=>['1'=>'已发出','2'=>'未发出'],
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'label'=>'中奖时间',
        'width'=>'150px',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],

];

echo GridView::widget([
    'id'=>'prize_record',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[
        [
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar'=> [
        [
            //'content'=> Html::button('新增商品',['type'=>'button','title'=>'新增商品', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('goods/create').'";return false;']),

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
    'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]],
    'panel' => [
        'type' => GridView::TYPE_INFO
    ],

]);

$js='
$("#prize_record-pjax").on("click",".delete",function(){
    if(!confirm("确定要删除该记录吗？"))
    {
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
                if(data.code == "0")
                {
                    $("#prize_record").yiiGridView("applyFilter");
                 }
                 else
                 {
                     alert("删除失败：" + data.msg);
                     //window.location.reload()
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
$this->registerJs($js,\yii\web\View::POS_END);