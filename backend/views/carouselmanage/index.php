<style>
    .user-pic
    {
        width: 150px;
        /*border-radius: 50%;*/
    }
</style>

<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:38
 */
use kartik\grid\GridView;
use yii\bootstrap\Html;

$this->params['activity_types'] = $activity_type;
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'title',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'discribtion',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'activity_type',
        'vAlign' => 'middle',
        'value' => function($model)
        {


            return $this->params['activity_types'][$model->activity_type];
        },
        'filter'=>$activity_type
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'width'=>'100px',
        'vAlign'=>'middle',
        'attribute'=>'status',
        'value'=>function($model)
        {
            return $model->GetStatusName();
        },
        'filter'=>['0'=>'禁用','1'=>'正常','2'=>'审核中'],
        'headerOptions'=>['class'=>'kv-sticky-column'],
        'contentOptions'=>['class'=>'kv-sticky-column'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/carouselmanage/setstatus?carousel_id='.strval($model->carousel_id)],
                'header'=>'状态',
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig'=>['0'=>'禁止','1'=>'正常','2'=>'审核中'],
                'data'=>['0'=>'禁止','1'=>'正常','2'=>'审核中'],
            ];
        },
        'refreshGrid'=>true,
    ],
    [
        'attribute'=>'action_type',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return $model->GetActionType();
        },
        'filter'=>['0'=>'无','1'=>'搜索','2'=>'链接跳转','3'=>'网页链接','4'=>'下载APP链接'],
    ],
    [
        'format'=>'html',
        'attribute'=>'pic_url',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            $url = empty($model->pic_url)?'http://oss.aliyuncs.com/meiyuan/wish_type/default.png':$model->pic_url;
            return Html::img($url,['class'=>'user-pic']);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'order_no',
        'vAlign'=>'middle',
        'filter'=>false,
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
                    $url = '/carouselmanage/update?carousel_id='.strval($model->carousel_id);
                    break;
                case 'delete':
                    $url = '/carouselmanage/delete?carousel_id='.strval($model->carousel_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-confirm'=>'确定要删除该记录吗？', 'data-toggle'=>false,'data-pjax'=>'1'],
        'buttons'=>[
            'update'=>function($url,$model)
            {
                return Html::a('编辑',$url);
            },
        ],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'id'=>'user-manage-list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:500px;'], // only set when $responsive = false
    'beforeHeader'=>[
        [
//            'columns'=>[
//                ['content'=>'Header Before 1', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 2', 'options'=>['colspan'=>4, 'class'=>'text-center warning']],
//                ['content'=>'Header Before 3', 'options'=>['colspan'=>3, 'class'=>'text-center warning']],
//            ],
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar' =>  [
        ['content'=>
            Html::button('新增轮播图', ['type'=>'button', 'title'=>'新增用户', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('carouselmanage/create').'";return false;']),// . ' '.
            //Html::a('&lt;i class="glyphicon glyphicon-repeat">&lt;/i>', ['grid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
        ],
        //'{export}',
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
    //'floatHeader' => true,
    //'floatHeaderOptions' => ['scrollingTop' => '300px'],
    //'showPageSummary' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);

echo \yii\bootstrap\Modal::widget([
        'id' => 'contact-modal',
        'clientOptions' => false,
        'size'=>\yii\bootstrap\Modal::SIZE_LARGE,
    ]
);
$js='
$(".user-del").on("click",function(){
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
                     $("#user-manage-list").yiiGridView("applyFilter");
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
});
';
$this->registerJs($js,\yii\web\View::POS_END);