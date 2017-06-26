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


$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'class' => 'kartik\grid\EditableColumn',
        'width'=>'100px',
        'attribute'=>'status',
        'value'=>function($model)
        {
            return $model->GetStatusName();
        },
        'filter'=>['1'=>'未处理','2'=>'已处理'],
        'headerOptions'=>['class'=>'kv-sticky-column'],
        'contentOptions'=>['class'=>'kv-sticky-column'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/reportmanage/setstatus?my_report_id='.strval($model->my_report_id)],
                'header'=>'状态',
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig'=>['1'=>'未处理','2'=>'已处理'],
                'data'=>['1'=>'未处理','2'=>'已处理'],
            ];
        },
        'readonly'=>function($model)
        {
            return $model->status === 2? true:false;
        },
        'refreshGrid'=>true,
    ],
    'user_id',
    'nick_name',
    [
        'attribute'=>'report_type',
        'value'=>function($model)
        {
            return $model->GetReportTypeName();
        },//1 欺诈  2 色情  3 政治谣言 4 常识性谣言 5 恶意营销 6 其他侵权（冒名、诽谤、抄袭
        'filter'=>['1'=>'欺诈','2'=>'色情','3'=>'政治谣言','4'=>'常识性谣言','5'=>'恶意营销','6'=>'其他侵权'],
    ],
    'wish_id',
    'wish_name',
    'report_user_id',
    'report_user_name',
    'report_content',
    [
        'attribute'=>'create_time',
        'vAlign'=>'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
    ],
    [
        'width'=>'161px',
        'attribute'=>'check_time',
        'filter'=>false,
    ],
    /*[
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
                    $url = '/hotwords/update?hot_words_id='.strval($model->hot_words_id);
                    break;
                case 'delete':
                    $url = '/hotwords/delete?hot_words_id='.strval($model->hot_words_id);
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
    ],*/
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
        ['content'=>'',
           // Html::button('新增热词', ['type'=>'button', 'title'=>'新增用户', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('hotwords/create').'";return false;']),// . ' '.
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
/*$js='
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
$this->registerJs($js,\yii\web\View::POS_END);*/