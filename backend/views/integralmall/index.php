<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 14:05
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'gift_order',
        'vAlign'=>'middle',
        'label'=>'礼品排序号',
        'width'=>'300px',
    ],
    [
        'attribute'=>'gift_name',
        'vAlign'=>'middle',
        'label'=>'礼品名称',
        'width'=>'350px',
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'gift_money',
        'vAlign'=>'middle',
        'label'=>'礼物真实价格',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'gift_money',
                'formOptions'=>['action'=>'/integralmall/set_money?record_id='.strval($model['record_id'])],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                'value'=>$model['gift_money'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'gift_integral',
        'vAlign'=>'middle',
        'label'=>'兑换所需积分',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'gift_integral',
                'formOptions'=>['action'=>'/integralmall/set_integral?record_id='.strval($model['record_id'])],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                'value'=>$model['gift_integral'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'gift_pic',
        'vAlign'=>'middle',
        'label'=>'礼品图片',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = $model['gift_pic'];
            return Html::img($url,['class'=>'pic','style'=>'width:50px']);
        }
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'gift_num',
        'vAlign'=>'middle',
        'label'=>'礼品剩余数量',
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'gift_num',
                'formOptions'=>['action'=>'/integralmall/set_gift_num?record_id='.strval($model['record_id'])],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                'value'=>$model['gift_num'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'gift_send_num',
        'vAlign'=>'middle',
        'label'=>'礼品数量',
        'width'=>'300px',
    ],
    [
        'attribute'=>'gift_details',
        'vAlign'=>'middle',
        'label'=>'礼品介绍',
        'width'=>'350px',
    ],
    [
        'attribute'=>'gift_grant',
        'vAlign'=>'middle',
        'label'=>'礼品发布',
        'width'=>'350px',
    ],
    [
        'attribute'=>'gift_accept',
        'vAlign'=>'middle',
        'label'=>'礼品领奖提示',
        'width'=>'350px',
    ],
    [
        'attribute'=>'gift_type',
        'vAlign'=>'middle',
        'label'=>'礼品类型',
        'width'=>'200px',
        'value'=>function($model)
        {
            switch($model['gift_type'])
            {
                case 1:
                    $rst = '官方虚拟物品-游戏币';
                    break;
                case 2:
                    $rst = '官方虚拟物品-鲜花';
                    break;
                case 3:
                    $rst = '官方虚拟物品-经验值';
                    break;
                case 200:
                    $rst = '虚拟物品';
                    break;
                case 300:
                    $rst = '红包';
                    break;
                case 400:
                    $rst = '实际物品';
                    break;
            }
            return $rst;
        },
        'filter'=>['1'=>'官方虚拟物品-游戏币','2'=>'官方虚拟物品-鲜花','3'=>'官方虚拟物品-经验值','200'=>'虚拟物品','300'=>'红包','400'=>'实际物品'],
    ],

    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'delete':
                    $url = '/integralmall/delete?record_id='.strval($model['record_id']);
                    break;
                case 'update':
                    $url = '/integralmall/update?gift_order='.strval($model['gift_order']);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'delete' =>function ($url, $model,$key)
            {
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-pjax'=>'0','style'=>'margin-left:10px']);
            },
            'update' => function ($url, $model, $key)
            {
                return Html::a('编辑',$url,[ 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
            },
        ],
    ],
];

echo GridView::widget([
    'id'=>'gift_score_list',
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
            'content'=> Html::button('新增礼品',['type'=>'button','title'=>'新增礼品', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('integralmall/create').'";return false;']),
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
$("#gift_score_list-pjax").on("click",".delete",function(){
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
                    $("#gift_score_list").yiiGridView("applyFilter");
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




