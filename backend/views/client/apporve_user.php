<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/14
 * Time: 17:18
 */

use kartik\grid\GridView;
use yii\bootstrap\Html;

$gridColumns = [
    [
        'attribute'=>'client_id',
        'vAlign'=>'middle',
        'label' => '用户id',
        'width' => '150px'
    ],
    [
        'attribute'=>'client_no',
        'vAlign'=>'middle',
        'label' => '蜜播id',
        'width' => '150px'
    ],
    [
        'attribute'=>'is_centification',
        'vAlign'=>'middle',
        'width'=>'200px',
        'label'=>'认证类型',
        'value'=>function($model)
        {
            switch($model['is_centification'])
            {
                case 1:
                    $type = '未认证';
                    break;
                case 2:
                    $type = '高级认证';
                    break;
                case 3:
                    $type = '高级认证审核';
                    break;
                case 4:
                    $type = '低级认证';
                    break;
                case 5:
                    $type = '低级认证审核';
                    break;
            }
            return $type;
        },
        'filter'=>['1'=>'未认证','2'=>'高级认证','3'=>'高级认证审核','4'=>'低级认证','5'=>'低级认证审核'],
    ],
    [
        'attribute'=>'actual_name',
        'vAlign'=>'middle',
        'label' => '用户姓名',
        'width' => '200px'
    ],
    [
        'attribute'=>'id_card',
        'vAlign'=>'middle',
        'label' => '身份证'
    ]
];
?>
<?php
echo GridView::widget([
    'id'=>'goods_list',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto;height:480px;font-size:14px;'],
    'beforeHeader'=>[
        [
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar'=> [
        [
            //'content'=> Html::button('返回',['type'=>'button','title'=>'返回', 'class'=>'btn btn-success return_client']),
        ],
        //'{export}',
        /*'{toggleData}',
        'toggleDataContainer' => ['class' => 'btn-group-sm'],
        'exportContainer' => ['class' => 'btn-group-sm']*/

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
';
$this->registerJs($js,\yii\web\View::POS_END);