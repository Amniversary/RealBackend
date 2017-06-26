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
        'format'=>'html',
        'attribute'=>'image_url',
        'vAlign'=>'middle',
        'label' => '缩略图',
        'value'=>function($model)
        {
            $url = empty($model->image_url)?'http://oss.aliyuncs.com/meiyuan/wish_type/default.png':$model->image_url;
            return Html::img($url,['class'=>'user-pic','style' => 'width:80px;height:80px;']);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'weights',
        'label' => '权重',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'description',
        'vAlign'=>'middle',
        'label' => '描述',
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'width'=>'100px',
        'vAlign'=>'middle',
        'attribute'=>'status',
        'label' => '状态',
        'value'=>function($model)
        {
            return $model->status == 1?'正常':'禁用';
        },
        'filter'=>['0'=>'禁用','1'=>'正常'],
        'headerOptions'=>['class'=>'kv-sticky-column'],
        'contentOptions'=>['class'=>'kv-sticky-column'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/advertisingmanage/setstatus?ad_id='.strval($model->ad_id)],
                'header'=>'状态',
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig'=>['0'=>'禁用','1'=>'正常'],
                'data'=>['0'=>'禁用','1'=>'正常'],
            ];
        },
        'refreshGrid'=>true,
    ],

    [
        'attribute'=>'start_time',
        'vAlign'=>'middle',
        'label' => '开始时间',
        'filterType'=>'\kartik\datetime\DateTimePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'pluginOptions' => [
                'autoclose' => true,
                'readonly' => true,
                'linkFormat' => 'HH:ii P', // if inline = true
                // 'format' => 'HH:ii P', // if inline = false
                'todayBtn' => true
            ],
        ],
    ],
    [
        'attribute'=>'end_time',
        'vAlign'=>'middle',
        'label' => '结束时间',
        'filterType'=>'\kartik\datetime\DateTimePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'pluginOptions' => [
                'autoclose' => true,
                'readonly' => true,
                'linkFormat' => 'HH:ii P', // if inline = true
                // 'format' => 'HH:ii P', // if inline = false
                'todayBtn' => true
            ],
        ],
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
                    $url = '/advertisingmanage/update?ad_id='.strval($model->ad_id);
                    break;
                case 'delete':
                    $url = '/advertisingmanage/delete?ad_id='.strval($model->ad_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'update'=>function($url,$model)
            {
                return Html::a('编辑',$url);
            },
            'delete' =>function ($url, $model,$key)
            {
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-pjax'=>'0','style'=>'margin-left:10px']);
            },
        ],
    ],
//    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'id'=>'ad_image_list',
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
            Html::button('新增弹窗广告', ['type'=>'button', 'title'=>'新增弹窗广告', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('advertisingmanage/create').'";return false;']),// . ' '.
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
$(".delete").on("click",function(){
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
                     $("#ad_image_list").yiiGridView("applyFilter");
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