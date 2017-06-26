<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 15:33
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    ['class'=>'kartik\grid\SerialColumn'],
    [
        'attribute'=>'family_user_name',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'family_name',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'family_num',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'pic',
        'vAlign'=>'middle',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = $model->pic;
            return Html::img($url,['class'=>'pic','style'=>'width:50px']);
        }
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return $model->GetFamilyStatus();
        },
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/family/set_status?family_id='.strval($model->family_id)],
                'size'=>'min',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'data'=>['1'=>'正常','0'=>'禁用'],
            ];
        },
        'filter'=>['1'=>'正常','0'=>'禁用'],
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
    ],
    [
        'width'=>'300px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;{reset_pwd}&nbsp;&nbsp;{son}&nbsp;&nbsp;',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'reset_pwd':
                    $url = '/family/reset_pwd?family_id='.strval($model->family_id);
                    break;

                case 'update':
                    $url = '/family/update?family_id='.strval($model->family_id);
                    break;

//                case 'delete':
//                    $url = '/family/delete?family_id='.strval($model->family_id);
//                    break;
                case 'son':
                    $page = \Yii::$app->request->get('page');
                    if(empty($page))
                    {
                        $page = 1;
                    }
                    $url = '/family/index_son?family_id='.strval($model->family_id).'&page='.$page;
                    break;
            }
            return $url;
        },
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'buttons'=>[
            'reset_pwd' =>function ($url , $model, $key)
            {
                return Html::a('重置密码',$url,['data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px;']);
            },
//            'delete' =>function ($url, $model,$key)
//            {
//                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-pjax'=>'0','style'=>'margin-left:10px']);
//            },
            'son' =>function ($url, $model, $key)
            {
                return Html::a('成员管理',$url,['data-target'=>false,'style'=>'margin-left:10px']);
            },
            'update' =>function ($url, $model, $key)
            {
                return Html::a('编辑',$url,['data-target'=>'#contact-modal','style'=>'margin-left:10px']);
            }
        ],
    ],
];

echo GridView::widget([
    'id'=>'family_list',
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
            'content'=> Html::button('新增家族',['type'=>'button','title'=>'新增家族', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('family/create').'";return false;']),
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

echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);

$js='
$("#family_list-pjax").on("click",".delete",function(){
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
                    $("#w0-container").yiiGridView("applyFilter");
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