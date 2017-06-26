<style>
    .user-pic
    {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    #btn-style{

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
        'attribute'=>'username',
        'label'=>'用户名',
    ],
    [
        'attribute'=>'email',
        'label'=>'邮箱',
    ],
    [
        'label'=>'头像',
        'format'=>'html',
        'attribute'=>'pic',
        'vAlign'=>'middle',
        'value'=>function($model)
        {
            $url = empty($model->pic)?'http://oss.aliyuncs.com/meiyuan/wish_type/default.png':$model->pic;
            return Html::img($url,['class'=>'user-pic']);
        },
        'filter'=>false,
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'width'=>'100px',
        'attribute'=>'status',
        'value'=>function($model)
        {
            return $model->GetStatusName();
        },
        'filter'=>['0'=>'禁用','1'=>'正常'],
        'headerOptions'=>['class'=>'kv-sticky-column'],
        'contentOptions'=>['class'=>'kv-sticky-column'],
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/usermanage/setstatus?user_id='.strval($model->backend_user_id)],
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
        'label'=>'创建时间',
        'attribute'=>'create_at',
        'value'=>function($model)
        {
            return date('Y-m-d H:i:s',intval($model->create_at));
        },
        'filter'=>false
    ],
    [
        'label'=>'登录时间',
        'attribute'=>'update_at',
        'value'=>function($model){
            return date('Y-m-d H:i:s',intval($model->update_at));
        },
        'filter'=>false
    ],
    [
        'width'=>'300px',
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{update}&nbsp;&nbsp;{delete}&nbsp;&nbsp;{resetpwd}&nbsp;&nbsp;{setprivilige}',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {
                case 'update':
                    $url = '/usermanage/update?user_id='.strval($model->backend_user_id);
                    break;
                case 'delete':
                    $url = '/usermanage/delete?user_id='.strval($model->backend_user_id);
                    break;
                case 'resetpwd':
                    $url = '/usermanage/resetpwd?user_id='.strval($model->backend_user_id);
                    break;
                case 'setprivilige':
                    $url='/usermanage/getprivilige?user_id='.strval($model->backend_user_id);
                    break;
            }
            return $url;
        },
        'viewOptions'=>['title'=>'查看', 'data-toggle'=>'tooltip'],
        'updateOptions'=>['title'=>'编辑','label'=>'编辑', 'data-toggle'=>false],
        'deleteOptions'=>['title'=>'删除','label'=>'删除','data-toggle'=>false],
        'buttons'=>[
            'resetpwd' => function ($url, $model, $key) {
                return Html::a('重置密码',$url,[ 'data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px;']);
            },
            /*'setcheckno' => function ($url, $model, $key) {
                return Html::a('设置审核号',$url,[ 'data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px;']);
            },*/
            'update'=>function($url,$model)
            {
                if($model->backend_user_id === 1 || $model->username === 'admin') return '';
                return Html::a('编辑',$url);
            },
            'delete'=>function($url,$model)
            {
                if($model->backend_user_id === 1 || $model->username === 'admin') return '';
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-method'=>'post', 'data-pjax'=>'1']);
            },
            'setprivilige'=>function($url, $model,$key)
            {
                return Html::a('权限',$url,[ 'data-toggle'=>'modal','data-target'=>'#contact-modal','style'=>'margin-left:10px;']);
            }
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
            Html::button('新增用户', ['type'=>'button', 'title'=>'新增用户', 'class'=>'btn btn-success','id'=>'btn-style', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('usermanage/create').'";return false;']),// . ' '.
            //Html::a('&lt;i class="glyphicon glyphicon-repeat">&lt;/i>', ['grid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
        ],
        //'{export}',
        //'{toggleData}',
        //'toggleDataContainer' => ['class' => 'btn-group-sm'],
        //'exportContainer' => ['class' => 'btn-group-sm']
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