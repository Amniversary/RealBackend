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
        padding: 6px 12px;
    }
</style>
<?php
use kartik\grid\GridView;
use yii\bootstrap\Html;

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
        'class'=>'kartik\grid\ActionColumn',
        'template'=>'{son}&nbsp;&nbsp;&nbsp;{click}&nbsp;&nbsp;&nbsp;{update}&nbsp;&nbsp;&nbsp;{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'200px',
        'urlCreator'=>function($action, $model, $key, $index){
            $url = '';
            switch ($action){
                case 'update':
                    $url = '/custom/updateson?menu_id='.strval($model->menu_id).'&parent_id='.strval($model->parent_id);
                    break;
                case 'delete':
                    $url = '/custom/deleteson?menu_id='.strval($model->menu_id);
                    break;
                case 'click':
                    $url = '/custom/customson_msg?menu_id='.strval($model->menu_id).'&parent_id='.strval($model->parent_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'修改','label'=>'修改', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'update'=>function($url, $model){
                return Html::a('修改', $url,['class'=>'back-a']);
            },
            'delete'=>function($url, $model){
                return Html::a('删除',$url,['class'=>'delete back-a','data-toggle'=>false,'data-pjax'=>'0']);
            },
            'click'=>function($url, $model){
                if($model->type == 'click') {
                    return Html::a('消息配置', $url, ['class'=>'back-a']);
                }
                return '';
            }
        ],
    ]
];

echo GridView::widget([
    'id'=>'customson_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('返回',['type'=>'button','class'=>'btn btn-success','onclick'=>'location="'.\Yii::$app->urlManager->createUrl('custom/index').'";return false;']).'&nbsp;&nbsp;&nbsp;&nbsp;'.
                Html::button('新增子菜单',['id'=>'create-menu','type'=>'button', 'class'=>'btn btn-success']),
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
    $url = "http://"+ window.location.host + "/custom/check_menu_son?menu_id='.$menu_id.'";
    $.ajax({
        type:"POST",
        url:$url,
        data: "",
        success: function(data){
            data = $.parseJSON(data);
            if(data.code == 0){
                location = "'.\Yii::$app->urlManager->createUrl(['custom/createson','menu_id'=>$menu_id]).'";
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
$("#customson_list-pjax").on("click",".delete",function(){
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
                    $("#customson_list").yiiGridView("applyFilter");
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