<style>
    .alert{
        padding: 10px;

    }
    .content-header{
        position: relative;
        padding: 1px 15px 0 15px;
    }

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
/**
 *  @var $model common\models\SystemMenu
 */
$gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'],
    [
        'attribute'=>'deploy_name',
        'vAlign'=>'middle',
        'filter'=>false,
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'value'=>function($model){
            return  ($model->status == 1 ? '正常' : '禁用');
        },
        'filter'=>false,
        'headerOptions'=>['class'=>'kv-sticky-column'],
        'contentOptions'=>['class'=>'kv-sticky-column'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/batchcustom/status?id='.strval($model->id)],
                'header'=>'状态',
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig'=>['0'=>'禁止','1'=>'正常'],
                'data'=>['0'=>'禁止','1'=>'正常'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class'=>'kartik\grid\ActionColumn',
        'template'=>'{setauth}{create_menu}{delete}',
        'dropdown'=>false,
        'vAlign'=>'middle',
        'width'=>'300px',
        'urlCreator'=>function($action, $model, $key, $index){
            $url = '';
            switch ($action){
                case 'create_menu':
                    $url = '/batchcustom/index_menu?id='.strval($model->id);;
                    break;
                case 'setauth':
                    $url = '/batchcustom/getauthlist?id='.strval($model->id);
                    break;
                case 'delete':
                    $url = '/batchcustom/delete?id='.strval($model->id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'delete'=>function($url, $model){
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete back-a','data-toggle'=>false,'data-pjax'=>'0']);
            },
            'create_menu'=>function($url,$model){
                return Html::a('菜单设置',$url,['class'=>'back-a','style'=>'margin-right:10px']);
            },
            'setauth'=>function($url ,$model){
                return Html::a('设置公众号',$url,['class'=>'back-a','style'=>'margin-right:10px','data-toggle'=>'modal','data-target'=>'#contact-modal']);
            }
        ],
    ]
];

echo GridView::widget([
    'id'=>'batch_custom_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' =>$gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
    'beforeHeader'=>[['options'=>['class'=>'skip-export']]],
    'toolbar'=> [
        [
            'content'=> Html::button('添加配置',['type'=>'button', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('batchcustom/create').'";return false;']),
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
echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);
$js='
$("#batch_custom_list-pjax").on("click",".delete",function(){
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
                    $("#batch_custom_list").yiiGridView("applyFilter");
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