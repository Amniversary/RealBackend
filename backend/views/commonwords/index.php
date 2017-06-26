<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/29
 * Time: 17:00
 */

\common\assets\ArtDialogAsset::register($this);
use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    [
        'attribute'=>'user_id',
        'vAlign'=>'middle',
        'label'=>'常用语类型',
        'width'=>'150px',
        'value'=>function($model)
        {
            $static = $model['user_id'] == 1?"通用型":'基本型';
            return $static;
        },
        'filter' => ['1'=>'通用型']
    ],
    [
        'attribute'=>'content',
        'vAlign'=>'middle',
        'label'=>'日常语内容',
        'width'=>'600px',
    ],
    [
        'attribute'=>'status',
        'vAlign'=>'middle',
        'label'=>'日常用语状态',
        'width'=>'150px',
        'value'=> function($model)
        {
            $static = '';
            switch($model['status'])
            {
                case 1:
                    $static = '正常';
                    break;
                case 2:
                    $static = '禁用';
                    break;
            }
            return $static;
        },
        'filter' => ['1'=>'正常','2'=>'禁用']
    ],
    [
        'attribute'=>'create_at',
        'vAlign'=>'middle',
        'label'=>'创建时间',
        'width'=>'150px',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'width'=>'310px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update_words}&nbsp;&nbsp;{delete_words}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update_words':
                    $url = '/commonwords/update?cid='.strval($model['cid']);
                    break;
                case 'delete_words':
                    $url = '/commonwords/delete?cid='.strval($model['cid']);
                    break;

            }
            return $url;
        },
        'buttons'=>[
            'update_words'=>function($url,$model)
            {
                return Html::a('编辑',$url,[ 'data-target'=>'#contact-modal','style'=>'margin-left:10px']);
            },
            'delete_words'=>function($url,$model)
            {
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-pjax'=>'0','style'=>'margin-left:10px']);
            },
        ],
    ],
];
?>
<?php
echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);


echo GridView::widget([
    'id'=>'user-manage-list',
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
            'content'=> Html::button('新增常用语',['type'=>'button','title'=>'新增常用语', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('commonwords/add').'";return false;']),
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


\yii\bootstrap\Modal::begin([
        'id' => 'multi-modal',
        'clientOptions' => false,
        'header' => Html::button('确定',['class' => 'btn btn-default','id'=>'set_finance']).' '.Html::button('取消',['aria-hidden'=>'true', 'class' => 'btn btn-default','data-dismiss'=>'modal']),
        'size'=>\yii\bootstrap\Modal::SIZE_SMALL,
    ]
);


$js='

$("body").on("click",".delete",function(){
    url = $(this).attr("href");
    if(!confirm("确定要删除该记录吗？"))
    {
        return false;
    }
    $.ajax({
        type: "POST",
        url: url,
        data: "",
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                    $("#user-manage-list").yiiGridView("applyFilter");
                    return false;
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