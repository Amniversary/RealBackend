<style>
    .back-a{
        display: inline-block;
        font-size: 14px;
        border-radius: 3px;
        color: #00a7d0;
        border:1px solid #00a7d0;
        padding: 3px 5px;
    }
    .back-btn{
        display: inline-block;
        font-size: 14px;
        margin-bottom: 0px;
        border-radius: 3px;
        color: #00a65a;
        border:1px solid #00a65a;
        padding: 3px 3px;
    }
</style>
<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;
/**
 *  @var $model common\models\Keywords
 *  @var $is_verify
 */
/*if(!$is_verify){
    echo \yii\bootstrap\Alert::widget([
        'body'=>'公众号未认证，无法进行相应操作！',
        'options'=>[
            'class'=>'alert-warning',
        ]
    ]);
}*/
$gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'],
    [
        'attribute'=>'name',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'type',
        'vAlign'=>'middle',
        'value'=>function($model){
            $rst = '';
            switch ($model->type){
                case 'click': $rst = '点击事件';break;
                case 'view': $rst = '跳转链接';break;
            }
            return empty($model->type) ? '': $rst;
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'key_type',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'attribute'=>'url',
        'vAlign'=>'middle',
        'value'=>function($model){
            return empty($model->url) ? '':  mb_substr($model->url, 0 ,15) . '....' ;
        },
        'filter'=>false
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute'=>'is_list',
        'vAlign'=>'middle',
        'value'=>function($model){
            return (($model->is_list == 0)? '否':'是');
        },
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/custom/islist?menu_id='.strval($model->menu_id)],
                'size'=>'min',
                'value'=>'is_list',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['0'=>'否','1'=>'是'],
            ];
        },
        'filter'=>false,
        'refreshGrid'=>true,
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{son}{click}{update}{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'250px',
        'urlCreator'=>function($action, $model, $key, $index){
            $url = '';
            switch ($action){
                case 'son':
                    $url = '/custom/indexson?menu_id='.strval($model->menu_id);
                    break;
                case 'update':
                    $url = '/custom/update?menu_id='.strval($model->menu_id);
                    break;
                case 'delete':
                    $url = '/custom/delete?menu_id='.strval($model->menu_id);
                    break;
                case 'click':
                    $url = '/custom/custom_msg?menu_id='.strval($model->menu_id);
                    break;
            }
            return $url;
        },
        'viewOptions'=>['title'=>'查看', 'data-toggle'=>'tooltip'],
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'son'=>function($url, $model){
                if($model->is_list == 1){
                    return Html::a('二级菜单管理', $url,['style'=>'margin-right:10px','class'=>'back-a','data-toggle'=>false]);
                }
                return '';
            },
            'update'=>function($url, $model){
                return Html::a('编辑',$url,['class'=>'back-a','style'=>'margin-right:10px']);
            },
            'delete'=>function($url, $model){
                return Html::a('删除',$url,['class'=>'delete back-a','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-method'=>'post', 'data-pjax'=>'1']);
            },
            'click'=>function($url, $model){
                if($model->type == 'click') {
                    return Html::a('消息配置', $url, ['class'=>'back-a','style'=>'margin-right:10px']);
                }
                return '';
            }
        ],
    ]


];

echo GridView::widget([
    'id'=>'custom_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=>
                Html::button('保存菜单', ['id'=>'save-menu','type'=>'button', 'class'=>'btn btn-success']).
                Html::button('加载菜单配置', ['type'=>'button', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('custom/download').'";return false;']).
                Html::button('删除菜单', ['type'=>'button' ,'class'=>'btn btn-success','id'=>'delete-menu']).
                Html::button('新增菜单',['id'=>'create-menu','type'=>'button','class'=>'btn btn-success']),
        ],
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']
    ],
    'pjax' => true,
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
$(document).on("click","#create-menu",function(){
    $url = "http://"+ window.location.host + "/custom/check";
    $.ajax({
        type:"POST",
        url:$url,
        data: "",
        success: function(data){
            data = $.parseJSON(data);
            if(data.code == "0"){
                location = "'.\Yii::$app->urlManager->createUrl('custom/create').'";
                return false;
            }else{
                alert(data.msg);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
        }
    })
});
$(document).on("click","#delete-menu",function(){
    $url = "http://"+ window.location.host +"/custom/delete_menu";
    $.ajax({
        type:"POST",
        url:$url,
        data:"",
        success: function(data){
            data = $.parseJSON(data);
            if(data.code == 0){
                alert("数据提交成功");
                $("#custom_list").yiiGridView("applyFilter");
            }else{
                alert("设置失败: " + data.msg);
                return false;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
        },
    });
});
$(document).on("click","#save-menu",function(){
    $url = "http://"+ window.location.host + "/custom/save";
    $.ajax({
        type:"POST",
        url:$url,
        data: "",
        success: function(data){
            data = $.parseJSON(data);
            if(data.code == "0"){
                alert(data.msg);
                $("#custom_list").yiiGridView("applyFilter");
            }else{
                alert(data.msg);
                return false;
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
        }
    })
    return false;
});
$("#custom_list-pjax").on("click",".delete",function(){
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
                    $("#custom_list").yiiGridView("applyFilter");
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
        return false;
});
';
$this->registerJs($js,\yii\web\View::POS_END);