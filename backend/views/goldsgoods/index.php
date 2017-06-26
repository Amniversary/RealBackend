<?php
/**
 * Created by PhpStorm.
 * User: WangWei
 * Date: 2016/10/19
 * Time: 13:55
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;


$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'gold_goods_id',
        'vAlign'=>'middle',
        'label'=>'金币商品ID',
    ],
    [
        'attribute'=>'gold_goods_name',
        'vAlign'=>'middle',
        'label'=>'金币商品名称',
    ],
    [
        'attribute'=>'gold_goods_pic',
        'vAlign'=>'middle',
        'format'=>'html',
        'label'=>'金币图标',
        'value'=>function($model){
            $url =   empty($model['gold_goods_pic'])?'http://oss.aliyuncs.com/meiyuan/wish_type/default.png':$model['gold_goods_pic'];
            return Html::img($url,['class'=>'user-pic','style'=>'width:40px']);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'gold_goods_price',
        'vAlign'=>'middle',
        'label'=>'金币单价',
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'sale_type',
        'vAlign'=>'middle',
        'value'=>function($model){
            return   sale_type($model['sale_type']);
        },
        'filter'=>['1'=>'内购','4'=>'所有','8'=>'外购'],
        'editableOptions'=>function($model){
            return [
                'formOptions'=>['action'=>'/goldsgoods/saletype?gold_goods_id='.strval($model->gold_goods_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'内购','4'=>'所有','8'=>'外购'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [  'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'width'=>'200px',
        'label'=>'状态',
        'value'=>function($model){
            return status($model['status']);
        },
        'filter'=>['0'=>'禁止','1'=>'正常'],
        'editableOptions'=>function($model){
            return [
                'formOptions'=>['action'=>'/goldsgoods/updatestatus?gold_goods_id='.strval($model->gold_goods_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['0'=>'禁止','1'=>'正常'],
            ];
        },
        'refreshGrid'=>true,        
    ],
    [  'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'gold_goods_type',
        'vAlign'=>'middle',
        'width'=>'200px',
        'label'=>'商品类型',
        'value'=>function($model){
            return gold_goods_type($model['gold_goods_type']);
        },
        'filter'=>['1'=>'无限制','2'=>'限量','3'=>'限期','4'=>'限量且限期'],
        'editableOptions'=>function($model){
            return [
                'formOptions'=>['action'=>'/goldsgoods/goodstype?gold_goods_id='.strval($model->gold_goods_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'无限制','2'=>'限量','3'=>'限期','4'=>'限量且限期'],
            ];
        },
        'refreshGrid'=>true, 
    ],
    [
        'attribute'=>'gold_num',
        'vAlign'=>'middle',
        'label'=>'金币量',
    ], 
    [
        'attribute'=>'extra_integral_num',
        'vAlign'=>'middle',
        'label'=>'赠送积分',
    ],
    [
        'attribute'=>'order_no',
        'vAlign'=>'middle',
        'label'=>'排序号',
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
                    $url = '/goldsgoods/edit?gold_goods_id='.strval($model['gold_goods_id']);
                    break;
                case 'delete':
                    $url = '/goldsgoods/delete?gold_goods_id='.strval($model['gold_goods_id']);
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
            'content'=> Html::button('新增金币商品',['type'=>'button','title'=>'新增金币商品', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('goldsgoods/create').'";return false;']),

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