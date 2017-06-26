<STYLE>
    .user-pic{
        cursor:pointer;
    }
</STYLE>
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/3
 * Time: 10:00
 */

\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    [
        'attribute'=>'client_id',
        'vAlign'=>'middle',
        'width'=>'200px',
        'label'=>'用户ID'
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'width'=>'200px',
        'label'=>'蜜播ID'
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'nick_name',
        'vAlign'=>'middle',
        'width'=>'250px',
        'editableOptions'=>function($model)
        {
            return [
                'formOptions' => ['action' => '/client/set_client_name?client_id='.$model['client_id']],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=> 'icon_pic',
        'vAlign'=>'middle',
        'format'=>'html',
        'label'=>'用户图片',
        'value'=>function($model)
        {
//            if($model->status == 0)
//            {
//                $url = 'http://mbpic.mblive.cn/meibo-test/feonghao.png';
//                return Html::img($url,['class'=>'user-pic','style'=>'width:100px','title'=>$url]);
//            }

            //获得大图标的地址,没有则使用原图地址
            $D_url = $model->main_pic;
            if($D_url == null)
            {
                $D_url = $model->pic;
            }
            //判断是否有小图标（有小图标则使用，没有使用原图）
            if($model->icon_pic == null)
            {
                $url = empty($model->pic)?'http://oss.aliyuncs.com/meiyuan/wish_type/default.png':$model->pic;
                if(!empty($model->icon_pic))
                {
                    $url = $model->icon_pic;
                }
                return Html::img($url,['class'=>'user-pic','style'=>'width:100px','title'=>$D_url]);
            }
            else
            {
                $url = $model->icon_pic;
                return Html::img($url,['class'=>'user-pic','style'=>'width:100px','title'=>$D_url]);
            }

        },
        'filter'=>false,
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute'=>'sign_name',
        'vAlign'=>'middle',
        'width'=>'250px',
        'editableOptions'=>function($model)
        {
            return [
                'formOptions' => ['action' => '/client/set_sign_name?client_id='.$model['client_id']],
                'size' => 'sm',
                'inputType' => \kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'width'=>'220px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{delete}{update}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            $ext_params = '';
            if(isset(\Yii::$app->params['client_pic_index']))
            {
                if(isset(\Yii::$app->params['client_pic_index']['page']))
                {
                    $ext_params .='&page='.\Yii::$app->params['client_pic_index']['page'];
                }
                if(isset(\Yii::$app->params['client_pic_index']['per-page']))
                {
                    $ext_params .='&per-page='.\Yii::$app->params['client_pic_index']['per-page'];
                }
            }
            switch($action)
            {
                case 'delete':
                    $url = '/client/delete_cover?client_no='.strval($model->client_no). $ext_params;
                    break;
                case 'update':
                    $url = '/client/updatepic?client_id='.strval($model->client_id). $ext_params;
                    break;
            }
            return $url;
        },
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'delete' =>function ($url, $model,$key)
            {
                return Html::a('删除',null,['title'=>'删除','class'=>'delete','style'=>'margin-left:10px;','rel'=>$url]);
            },
            'update' =>function ($url, $model,$key)
            {
                return Html::a('更新',null,['title'=>'更新','class'=>'update','style'=>'margin-left:10px;','href'=>$url]);
            },
        ],
    ],
];

echo GridView::widget([
    'id'=>'cover_list',
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
$("body").on("click",".user-pic",function(){
    var img = $(this).attr("title");
    if(img != ""){
        art.dialog({
            content: "<img style=\"width:640px;\" src=\" "+ img + " \">",
            title:"用户图片",
            cancelVal: "关闭",
            cancel: true //为true等价于function(){}
        });
    }
});


$("body").on("click",".delete",function(){
    $url = $(this).attr("rel");

    art.dialog.confirm("你确定要删除这张图片吗？", function () {
         $.ajax({
        type: "POST",
        url: $url,
        data: "",
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                     $("#cover_list").yiiGridView("applyFilter");
                 }
                 else
                 {
                     alert("删除失败：" + data.msg);
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
             }
        });
    }, function () {
    });

});


';
$this->registerJs($js,\yii\web\View::POS_END);





