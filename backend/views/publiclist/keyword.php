
<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    [
        'attribute'=>'record_id',
        'vAlign'=>'middle',
        'label'=>'#',
        'width'=>'80px',
    ],
    [
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'format'=>'html',
        'label'=>'公众号名称',
        'width'=>'150px',
        'value'=>function($model)
        {
            $url = empty($model->head_img)?'http://oss.aliyuncs.com/meiyuan/wish_type/default.png':$model->head_img;
            return Html::img($url,['class'=>'user-pic','style'=>'width:40px','value'=>$model->nick_name]).'&nbsp'. Html::label($model->nick_name);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'service_type_info',
        'vAlign'=>'middle',
        'width'=>'100px',
        'value'=>function($model){
            return $model->getServiceTypeInfo($model->service_type_info);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'verify_type_info',
        'vAlign'=>'middle',
        'value'=>function($model){
            return $model->getVerifyTypeInfo($model->verify_type_info);
        },
        'filter'=>false,
    ],
    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{wxbackend}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'wxbackend':
                    $url = '/publiclist/status?record_id='.$model->record_id;
                    break;
            }
            return $url;
        },
        'buttons'=>[
            'wxbackend' => function ($url, $model, $key) {
                $userId = Yii::$app->user->id;
                $cacheInfo = Yii::$app->cache->get('app_backend_'.$userId);
                $cacheInfo = json_decode($cacheInfo,true);
                if(!empty($cacheInfo && $model->record_id == $cacheInfo['record_id'])){
                    return Html::label('已选择','#',['class'=>'back-btn']);
                }else{
                    return Html::a('选择管理后台',$url,['class'=>'back-a','data-toggle'=>false]);
                }
            },
        ],
    ]
];

echo GridView::widget([
    'id'=>'keyword_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('添加公众号',['type'=>'button','title'=>'添加公众号', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('publiclist/create').'";return false;']),
        ],
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']
    ],
    //'pjax' => true,
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

$js='
$(".back-a").on("click",function(){
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
                    $("#keyword_list").yiiGridView("applyFilter");
                }
                else
                {
                    alert("选择失败：" + data.msg);
                    
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