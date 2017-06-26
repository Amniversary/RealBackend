<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 14:01
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;


$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'app_id',
        'vAlign'=>'middle',
        'label'=>'马甲号ID',
    ],
    [
        'attribute'=>'img_url',
        'vAlign'=>'middle',
        'format'=>'html',
        'label'=>'广告图片',
        'value'=>function($model){
            $url =   empty($model['img_url'])?'http://oss.aliyuncs.com/meiyuan/wish_type/default.png':$model['img_url'];
            return Html::img($url,['class'=>'user-pic','style'=>'width:70px']);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'width',
        'vAlign'=>'middle',
        'label'=>'图片宽度',
    ],
    [
        'attribute'=>'height',
        'vAlign'=>'middle',
        'label'=>'图片高度',
    ],
    [
        'attribute'=>'target_url',
        'vAlign'=>'middle',
        'label'=>'目标URL',
    ],
    [  'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label'=>'状态',
        'value' => function($model)
        {
            if( $model['status'] == 1 ){
                return '正常';
            }else if( $model['status'] == 2 ){
                return '过期';
            }

        },
        'filter'=>['1'=>'正常','2'=>'过期'],

        'editableOptions'=>function($model){
            return [
                'formOptions'=>['action'=>'/advertise/status?id='.strval($model->id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'正常','2'=>'过期'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'duration',
        'vAlign'=>'middle',
        'label'=>'广告时长(秒)',
    ],
    [
        'attribute'=>'effe_time',
        'vAlign'=>'middle',
        'label'=>'广告时间',
    ],
    [
        'attribute'=>'end_time',
        'vAlign'=>'middle',
        'label'=>'结束时间',
    ],
    [
        'attribute'=>'ordering',
        'vAlign'=>'middle',
        'label'=>'排序',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'width'=>'250px',
        'template'=>'{update}&nbsp;&nbsp;{delete}',
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index){
            $url = '';
            switch($action) {
                case 'update':
                    $url = '/advertise/update?id='.strval($model['id']);
                    break;
                case 'delete':
                    $url = '/advertise/delete?id='.strval($model['id']);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'帐户明细','label'=>'帐户明细', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'update'=>function($url,$model){
                return  Html::a('编辑',$url,[ ]);
            },
            'delete' =>function ($url, $model,$key){
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'style'=>'margin-left:10px']);
            },
        ],
    ],

];
echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

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
            'content'=> Html::button('新增广告',['type'=>'button','title'=>'新增广告', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('advertise/create').'";return false;']),

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

function sale_type($sale_type){
    if( $sale_type == 1 ){
        return '内购';
    }else if( $sale_type == 4 ){
        return '所有';
    }else if( $sale_type == 8 ){
        return '外购';
    }
}

function status($status){
    if( $status == 0 ){
        return '禁止';
    }else if( $status == 1 ){
        return '正常';
    }
}

function gold_goods_type($gold_goods_type){
    if( $gold_goods_type == 1 ){
        return '无限制';
    }else if( $gold_goods_type == 2 ){
        return '限量';
    }else if( $gold_goods_type == 3 ){
        return '限期';
    }else if( $gold_goods_type == 4 ){
        return '限量且限期';
    }
}


$js = '
$(function(){
$("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
});
';


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