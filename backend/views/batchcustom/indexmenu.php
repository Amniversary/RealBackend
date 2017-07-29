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
 *  @var $model common\models\AuthorizationMenu
 *  @var $is_verify
 */

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
            return empty($model->url) ? '':$model->url;
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
                'formOptions'=>['action'=>'/batchcustom/is_list?menu_id='.strval($model->menu_id)],
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
        'template'=>'{son}{update}{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'250px',
        'urlCreator'=>function($action, $model, $key, $index){
            $url = '';
            switch ($action){
                case 'son':
                    $url = '/batchcustom/indexson?menu_id='.strval($model->menu_id).'&id='.strval($model->global);
                    break;
                case 'update':
                    $url = '/batchcustom/update_menu?menu_id='.strval($model->menu_id).'&id='.strval($model->global);
                    break;
                case 'delete':
                    $url = '/batchcustom/delete_menu?menu_id='.strval($model->menu_id);
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
                    return Html::a('二级菜单管理', $url,['title'=>'修改信息','class'=>'back-a','data-toggle'=>false,'style'=>'margin-right:10px']);
                }
                return '';
            },
            'update'=>function($url, $model){
                return Html::a('编辑',$url,['class'=>'back-a','style'=>'margin-right:10px']);
            },
            'delete'=>function($url, $model){
                return Html::a('删除',$url,['class'=>'delete back-a','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-method'=>'post', 'data-pjax'=>'1']);
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
                Html::button('返回', ['type'=>'button', 'class'=>'btn btn-success','onclick'=>'location="'.\Yii::$app->urlManager->createUrl('batchcustom/index').'";return false;']).
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
    $url = "http://"+ window.location.host + "/batchcustom/check?id='.$id.'";
    $.ajax({
        type:"POST",
        url:$url,
        data: "",
        success: function(data){
            data = $.parseJSON(data);
            if(data.code == "0"){
                location = "'.\Yii::$app->urlManager->createUrl(['batchcustom/create_menu','id'=>$id]).'";
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