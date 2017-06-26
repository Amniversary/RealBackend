<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:48
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;
\common\assets\ArtDialogAsset::register($this);
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'app_id',
        'vAlign'=>'middle',
        'label' => 'app标识'
    ],
    [
        'attribute'=>'module_id',
        'vAlign'=>'middle',
        'label' => '模块id'
    ],
    [

        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'force_update',
        'vAlign'=>'middle',
        'label' => '强制更新',
        'value'=>function($model)
        {
            return (($model->force_update == '0')? '否':'是');
        },
        'filter'=>['0'=>'否','1'=>'是'],
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'force_update',
                'formOptions'=>['action'=>'/versionmanage/setstatusson?settype=1&update_id='.strval($model->update_id)],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['0'=>'否','1'=>'是'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [

        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label' => '审核',
        'value'=>function($model)
        {
            return (($model->status == '1')? '审核中':'已审核');
        },
        'editableOptions'=>function($model)
        {
            return [
                'name'=>'status',
                'formOptions'=>['action'=>'/versionmanage/setstatusson?settype=2&update_id='.strval($model->update_id)],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'审核中','2'=>'已审核'],
            ];
        },
        'filter'=>['1'=>'审核中','2'=>'已审核'],
        'refreshGrid'=>true,
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute'=>'app_version_inner',
        'vAlign'=>'middle',
        'label' => '内部版本号',
        'editableOptions' => function($model)
        {
            return [
                'formOptions'=>['action'=>'/versionmanage/set_version_inner?update_id='.strval($model->update_id)],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute'=>'is_register',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return (($model->is_register == 0) ? '否':'是');
        },
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/versionmanage/set_register?update_id='.strval($model->update_id)],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['0'=>'否','1'=>'是'],
            ];
        },
        'filter'=>['0'=>'否','1'=>'是'],
        'refreshGrid'=>true,
    ],
    [
        'width'=>'200px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;{detail}&nbsp;&nbsp{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/versionmanage/updateson?update_id='.strval($model->update_id);
                    break;
                case 'detail':
                    $url = '/versionmanage/detailson?update_id='.strval($model->update_id);
                    break;
                case 'delete':
                    $url = '/versionmanage/deleteson?update_id='.strval($model->update_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'update' => function ($url, $model, $key)
            {
                return Html::a('编辑',$url,[ 'style'=>'margin-left:10px']);
            },
            'detail' => function ($url, $model, $key)
            {
                return Html::a('详情',$url,[ 'data-target'=>'#contact-modal','data-toggle'=>"modal",'style'=>'margin-left:10px']);
            },
            'delete' => function ($url, $model, $key)
            {
                return Html::a('删除',$url,['style'=>'margin-left:10px','id'=>'goods_delete', "class"=>"goods_delete",]);
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
    'id'=>'sondetail',
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
            'content'=> Html::button('返回',['type'=>'button','title'=>$add_title, 'class'=>'btn btn-success','onclick'=>'location="'.\Yii::$app->urlManager->createUrl('versionmanage/index').'";return false;']).'&nbsp;&nbsp;&nbsp;&nbsp;'.Html::button($add_title,['type'=>'button','title'=>$add_title, 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl(['versionmanage/createson','app_id'=>$app_id]).'";return false;']),
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
$("#sondetail-pjax").on("click",".goods_delete",function(){
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
                    $("#sondetail").yiiGridView("applyFilter");
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


        $(function(){
            $("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
        });
';
$this->registerJs($js,\yii\web\View::POS_END);