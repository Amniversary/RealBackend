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
        'attribute'=>'pic',
        'vAlign'=>'middle',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = empty($model->pic)?'http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/bean.png':$model->pic;
            return Html::img($url,['class'=>'pic','style'=>'width:40px']);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'gift_name',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'gift_value',
        'vAlign'=>'middle',
    ],

    [
        'attribute'=>'special_effects',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'world_gift',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return (($model->world_gift == 2) ? '是':'否');
        },
        'filter'=>['1'=>'否','2'=>'是'],
    ],
    [
        'attribute'=>'lucky_gift',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return (($model->lucky_gift == 1) ? '是':'否');
        },
        'filter'=>['0'=>'否','1'=>'是'],
    ],
    [
        'attribute'=>'order_no',
        'vAlign'=>'middle',
    ],
    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;{black}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/gift/update?gift_id='.strval($model->gift_id).'&page='.\Yii::$app->request->get('page');
                    break;
//                case 'delete':
//                    $url = '/gift/delete?gift_id='.strval($model->gift_id);
//                    break;
                case 'black':
                    $url = '/gift/black?gift_id='.strval($model->gift_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'update' => function ($url, $model, $key)
            {
                return Html::a('编辑',$url,[ 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
            },
            'black' => function ($url, $model, $key)
            {
                return Html::a(($model->remark2=='0'?'正常':'禁用'),$url,[ 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
            },
//            'delete' =>function ($url, $model,$key)
//            {
//                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-pjax'=>'0','style'=>'margin-left:10px']);
//            },
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
            'content'=> Html::button($add_title,['type'=>'button','title'=>$add_title, 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('gift/create').'";return false;']),
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