<style>
    .user-pic
    {
        width: 130px;
        /*border-radius: 50%;*/
    }
</style>
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28
 * Time: 14:44
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'type',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            $type = require(\Yii::$app->getBasePath().'/config/TemplateConfig.php');
            $activity_type_one = array_shift($type);
            return $type[$model->type];
        },
        'width' => '200px',
    ],
    [
        'attribute'=>'title',
        'vAlign'=>'middle',
        'width' => '200px',
    ],
    [
        'attribute'=>'content',
        'vAlign'=>'middle',
        'width' => '200px',
    ],
    [
        'attribute'=>'pic',
        'vAlign'=>'middle',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = (isset($model->pic) ? $model->pic : '');
            return Html::img($url,['class'=>'user-pic']);
        }
    ],
    [
        'attribute'=>'url',
        'vAlign'=>'middle',
        'width' => '600px',
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'width'=>'150px',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'width'=>'250px',
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
                    $url = '/activityshare/update?share_id='.strval($model->share_id);
                    break;
                case 'delete':
                    $url = '/activityshare/delete?share_id='.strval($model->share_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-confirm'=>'确定要删除该记录吗？', 'data-toggle'=>false,'data-pjax'=>'1'],
        'buttons'=>[
            'update'=>function($url, $model, $key)
            {
                return Html::a('编辑',$url,['data-target'=>'#contact-modal','style'=>'margin-left:10px']);
            },
            'delete'=>function($url, $model, $key)
            {
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'style'=>'margin-left:10px']);
            }
        ],
    ],

];

echo GridView::widget([
    'id'=>'activity_share',
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
            'content'=> Html::button('新增分享信息',['type'=>'button','title'=>'新增分享信息', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('activityshare/create').'";return false;']),

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
$("#activity_share-pjax").on("click",".delete",function(){
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
                    $("#activity_share").yiiGridView("applyFilter");
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