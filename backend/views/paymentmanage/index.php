<style type="text/css">
    .app_id_list > label {
        height: auto;
        min-width: 320px;
    }
    .app_id_list {
        height: 350px;
        overflow-y: auto;
    }
    .kv-cell-max-width {
        max-width: 450px;
    }
    .kv-cell-exe-width {
        min-width: 100px;
    }
</style>
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:48
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;


use common\models\MultiVersionInfo;

$applist =MultiVersionInfo::find()->select(['app_id','app_name'])->all();
foreach ($applist as $key=>$app ){
    $appListInfo[$app->app_id] = $app->app_name."($app->app_id)";
}

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute'=>'icon',
        'vAlign'=>'middle',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = empty($model->icon)?'http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/balance_pay.png':$model->icon;
            return Html::img($url,['class'=>'pic','style'=>'width:40px']);
        },
        'filter'=>false,
    ],
    [
        'attribute'=>'title',
        'vAlign'=>'middle',
    ],
    [
        'attribute'=>'code',
        'vAlign'=>'middle',
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'status',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            $rst= '未知';
            switch($model->status)
            {
                case 0:
                    $rst='禁用';
                    break;
                case 1:
                    $rst='正常';
                    break;
                case 2:
                    $rst='审核中';
                    break;
            }
            return $rst;
        },
        'filter'=>['0'=>'禁用','1'=>'正常','2'=>'审核中'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/paymentmanage/setstatus?record_id='.strval($model->payment_id)],
                'header'=>'状态',
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig'=>['0'=>'禁用','1'=>'正常','2'=>'审核中'],
                'data'=>['0'=>'禁用','1'=>'正常','2'=>'审核中'],
            ];
        },
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'app_type',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            if( $model->app_type ==1 ){
                $rst='正式版app';
            }else if( $model->app_type ==2 ){
                $rst='马甲号';
            }else if( $model->app_type ==3 ){
                $rst='所有';
            }
            return $rst;
        },
        'filter'=>['1'=>'正式版app','2'=>'马甲号','3'=>'所有'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/paymentmanage/apptype?record_id='.strval($model->payment_id)],
                'header'=>'状态',
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig'=>['1'=>'正式版app','2'=>'马甲号','3'=>'所有'],
                'data'=>['1'=>'正式版app','2'=>'马甲号','3'=>'所有'],
            ];
        },
    ],
    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'applied_ios',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            return $model->applied_ios ? '正常' : '禁用';
        },
        'editableOptions'=>function($model)
        {
            $status = [
                0 => '禁用',
                1 => '正常',
            ];
            return [
                'formOptions'=>['action'=>'/paymentmanage/appliedios?record_id='.strval($model->payment_id)],
                'size'=>'md',
                'inputType'=>\kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig'=> $status,
                'data'=> $status,
            ];
        },
    ],

    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'app_id',
        'vAlign'=>'middle',
        'contentOptions'=>['class'=>'kv-cell-max-width'],

        'editableOptions' => function($model) use($appListInfo)
        {
            return [
                'formOptions'=>[
                    'action'=>'/paymentmanage/setappid?paymentid='.strval($model->payment_id),

                ],
                'placement' => 'left',
                'size' => 'md',
                'options' => ['class' => 'app_id_list'],
                'inputType' => \kartik\editable\Editable::INPUT_CHECKBOX_LIST,

                'data' => $appListInfo,
            ];
        },
        'value' => function($model) use($appListInfo) {
            !is_array($model->app_id) && $model->app_id = json_decode($model->app_id, true);
            $value = $model->app_id;
            if (empty($value)) {
                $model->app_id = null;
                return null;
            }
            $types = [];
            foreach ($value as $i) {
                isset($appListInfo[$i]) && $types[] = $appListInfo[$i];
            }
            return implode('，', $types);
        },
        'refreshGrid'=>true,
    ],

    [
        'attribute'=>'order_no',
        'vAlign'=>'middle',
    ],
    [
        'width'=>'70px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;{delete}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'contentOptions'=>['class'=>'kv-cell-exe-width'],
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/paymentmanage/update?record_id='.strval($model->payment_id);
                    break;
                case 'delete':
                    $url = '/paymentmanage/delete?record_id='.strval($model->payment_id);
                    break;
            }
            return $url;
        },
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除', 'data-toggle'=>false],
        'buttons'=>[
            'update' => function ($url, $model, $key)
            {
                return Html::a('编辑',$url,['style'=>'margin-left:10px']);
            },
            'delete' =>function ($url, $model,$key)
            {
                return Html::a('删除',$url,[ 'style'=>'margin-left:10px','id'=>'goods_delete','data-confirm'=>'确定要删除该记录吗？']);
            },
        ],
    ],
];

echo GridView::widget([
    'id'=>'goods_list',
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
            'content'=> Html::button('新增支付方式',['type'=>'button','title'=>'新增支付方式', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('paymentmanage/create').'";return false;']),
        ],
        '{export}',
        '{toggleData}',
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']

    ],
    'pjax' => true,  //异步封装
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true, //响应式 自适应网页
    'hover' => true,
    'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]], //控制导出文件列表
    'panel' => [
        'type' => GridView::TYPE_INFO
    ],

]);

$js='
$("#goods_delete").on("click",function(){
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
                     $("#goods_list").yiiGridView("applyFilter");
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