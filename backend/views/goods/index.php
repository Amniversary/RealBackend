<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:48
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'goods_name',
        'vAlign'=>'middle',
        'width'=>'200px',
    ],
    [
        'attribute'=>'pic',
        'vAlign'=>'middle',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = empty($model->pic)?'http://oss.aliyuncs.com/meiyuan/wish_type/default.png':$model->pic;
            return Html::img($url,['class'=>'user-pic','style'=>'width:40px']);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'goods_price',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'bean_num',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return (($model['bean_num'] == '')? '0':$model['bean_num']);
        }
    ],
    [
        'attribute'=>'extra_bean_num',
        'vAlign'=>'middle',
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'sale_type',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return $model->GetSaleType();
        },
        'filter'=>['1'=>'内购','4'=>'所有','8'=>'外购'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/goods/sale_type?goods_id='.strval($model->goods_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'内购','4'=>'所有','8'=>'外购'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return (($model['status'] == '1')? '正常':'禁用');
        },
        'filter'=>['0'=>'禁用','1'=>'正常'],
        //'headerOptions' =>['class'=>'kv-sticky-column'],
        //'contentOptions'=>['class'=>'kv-sticky-column'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/goods/status?goods_id='.strval($model->goods_id)],
                //'header'=>'状态',
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                //'displayValueConfig'=>['0'=>'禁止','1'=>'正常'],
                'data'=>['0'=>'禁止','1'=>'正常'],
            ];
        },
        'refreshGrid'=>true,

    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'goods_type',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return $model->GetGoodsType();
        },
        'filter'=>['1'=>'无限制','2'=>'限量','3'=>'限期','4'=>'限量且限期'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions' =>['action'=>'/goods/goods_type?goods_id='.strval($model->goods_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'无限制','2'=>'限量','3'=>'限期','4'=>'限量且限期'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'order_no',
        'vAlign'=>'middle',
        'editableOptions'=>function($model)
        {
            return [
                'formOptions' =>['action'=>'/goods/goods_order?goods_id='.strval($model->goods_id)],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'high_led',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return (($model->high_led == '1')? '是':'否');
        },
        'filter'=>['0'=>'否','1'=>'是'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/goods/goods_led?goods_id='.strval($model->goods_id)],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['0'=>'否','1'=>'是'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'width'=>'220px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/goods/update?goods_id='.strval($model->goods_id);
                    break;
                case 'delete':
                    $url = '/goods/delete?goods_id='.strval($model->goods_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'update' => function ($url, $model, $key)
            {
                return Html::a('编辑',$url,['data-target'=>'#contact-modal','style'=>'margin-left:10px']);
            },
            'delete' =>function ($url, $model,$key)
            {
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'style'=>'margin-left:10px']);
            },
        ],
    ],
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
    'toolbar'=> [
        [
            'content'=> Html::button('新增商品',['type'=>'button','title'=>'新增商品', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('goods/create').'";return false;']),

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
$("#goods_list-pjax").on("click",".delete",function(){
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
                    $("#goods_list").yiiGridView("applyFilter");
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