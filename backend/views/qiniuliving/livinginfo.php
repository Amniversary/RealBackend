<?php
/**
 * Created by PhpStorm.
 * User: zff
 * Date: 2016/8/8
 * Time: 16:00
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
\common\assets\ArtDialogAsset::register($this);
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'unique_no',
        'vAlign'=>'middle',
        'width'=>'200px',
        'label' =>'用户唯一号',
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label' =>'蜜播ID',
    ],
    [
        'attribute'=>'living_id',
        'vAlign'=>'middle',
        'label' =>'直播ID',
    ],
    [
        'attribute'=>'push_url',
        'vAlign'=>'middle',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = 'javascript:;';
            $value = isset($model['push_url'])?$model['push_url']:'(未设置)';
            return Html::a($value,$url,['class'=>'td']);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'pull_http_url',
        'vAlign'=>'middle',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = 'javascript:;';
            $value = isset($model['pull_rtmp_url'])?$model['pull_rtmp_url']:'(未设置)';
            return Html::a($value,$url,['class'=>'td']);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'pull_rtmp_url',
        'vAlign'=>'middle',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = 'javascript:;';
            $value = isset($model['pull_rtmp_url'])?$model['pull_rtmp_url']:'(未设置)';
            return Html::a($value,$url,['class'=>'td']);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'pull_hls_url',
        'vAlign'=>'middle',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = 'javascript:;';
            $value = isset($model['pull_hls_url'])?$model['pull_hls_url']:'(未设置)';
            return Html::a($value,$url,['class'=>'td']);
        },
        'filter'=>false,
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => '操作',
        'template' => '{detail}',
        'width' => '160px',
        'urlCreator' => function($action, $model, $key, $index){
            $url = 'javascript:;';
            return $url;
        },
        'buttons' => [
            'detail' => function($url, $model, $key){
                return Html::a(
                    '七牛流详情',$url,
                    //['update', 'id' => $key],
                    ['class'=>'detail','data-qiniu'=>($model['qiniu_info']),'data-clientno'=>($model['client_no'])]
                );
            }
        ]
    ]
];

echo GridView::widget([
    'id'=>'livinginfo_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'toolbar'=> [
        '{export}',
        '{toggleData}',
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']
    ],
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
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
?>
<style>
    .aui_content{
        display: block !important;
    }
    .break{
        width: 480px;
        word-wrap: break-word;
    }
    .td,.td:hover{
       display: inline-block;
       width: 230px;
       word-wrap: break-word;
        color: #333;
            cursor: default;
    }
</style>
<?php
$js='
$(document).on("click",".detail",function(){
    var qiniu_info = JSON.stringify($(this).data("qiniu"));
    qiniu_info = qiniu_info?qiniu_info:"七牛流不存在";
    var clientno = $(this).data("clientno");
    art.dialog({
        width:680,
        height:200,
        id: "shake-demo",
        title: "七牛流详情",
        content: "<div class=\"form-group clearfix\"><label class=\"col-sm-2 control-label text-right\">蜜播ID:</label><div class=\"col-sm-10\"><p>"+clientno+"</p></div></div><div class=\"form-group clearfix\"><label class=\"col-sm-2 control-label text-right\">七牛流:</label><div class=\"col-sm-10\"><p class=\"break\">"+qiniu_info+"</p></div></div>",
        lock: true,
        fixed: true,
        ok: function () {            
        },
        okValue: "确定",
    });
});
';
$this->registerJs($js,\yii\web\View::POS_END);



