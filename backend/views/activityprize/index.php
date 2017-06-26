<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use frontend\business\ActivityUtil;

$this->title = '奖品信息设置';
//$this->params['breadcrumbs'][] = ['label'=>'蜜播活动管理', 'url'=>['index']];
//$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
	['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'pic',
        'vAlign' => 'middle',
        'format'=>'html',
        'value'=>function($model)
        {
            $url = $model->pic;
            return Html::img($url,['class'=>'pic','style'=>'width:40px']);
        },
        'filter'=>false,
    ],
    [
        'attribute' => 'grade',
        'vAlign' => 'middle',
        'value' => function($model)
        {
            switch($model['grade']){
                case '1':
                    $model['grade'] = "一等奖";
                    break;
                case '2':
                    $model['grade'] = "二等奖";
                    break;
                case '3':
                    $model['grade'] = "三等奖";
                    break;
                case '4':
                    $model['grade'] = "四等奖";
                    break;
                case '5':
                    $model['grade'] = "五等奖";
                    break;
                case '6':
                    $model['grade'] = "六等奖";
                    break;
                case '7':
                    $model['grade'] = "七等奖";
                    break;
                case '8':
                    $model['grade'] = "八等奖";
                    break;
            }
            return $model['grade'];
        },
        'filter'=>['1'=>'一等奖','2'=>'二等奖','3'=>'三等奖','4'=>'四等奖','5'=>'五等奖','6'=>'六等奖','7'=>'七等奖','8'=>'八等奖']
    ],
    [
        'attribute' => 'gift_name',
        'vAlign' => 'middle',
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'number',
        'vAlign' => 'middle',
        'editableOptions'=>function($model)
        {
            return [
                'formOptions'=>['action'=>'/activityprize/set_attributes?prize_id='.strval($model->prize_id).'&field=number'],
                'size'=>'sm',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid' => true,
    ],
    [
        'attribute' => 'unit',
        'vAlign' => 'middle',
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'rate',
        'vAlign' => 'middle',
        'editableOptions' => function($model)
        {
            return [
                'formOptions' => ['action' => '/activityprize/set_attributes?prize_id='.strval($model->prize_id).'&field=rate'],
                'size' => 'sm',
                'inputType' => \kartik\editable\Editable::INPUT_TEXT,
            ];
        },
        'refreshGrid' => true,
    ],
    [
        'attribute' => 'total_number',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'last_number',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'type',
        'vAlign' => 'middle',
        'value' => function($model)
        {
            switch($model['type']){
                case '1':
                    $model['type'] = "鲜花";
                    break;
                case '2':
                    $model['type'] = "经验值";
                    break;
                case '3':
                    $model['type'] = "礼品包";
                    break;
                case '4':
                    $model['type'] = "实物";
                    break;
            }
            return $model['type'];
        },
        'filter'=>['1'=>'鲜花','2'=>'经验值','3'=>'礼品包','4'=>'实物']
    ],
    [
        'attribute' => 'order_no',
        'vAlign' => 'middle',
    ],
	[
		'class' => 'kartik\grid\ActionColumn',
		'header' => '操作',
		'template' => '{update}&emsp;&emsp;{delete}&emsp;&emsp;{detail}',
		'width' => '280px',
        'urlCreator' => function($action, $model, $key, $index)
        {
            $url = '';
            switch($action)
            {

                case 'delete':
                    $url = '/activityprize/delete?prize_id='.strval($model['prize_id']);
                    break;

            }
            return $url;
        },
		'buttons' => [
			'update' => function($url, $model, $key){
				return Html::a(
					'编辑',
					['update', 'prize_id' => $model['prize_id']],
                    [
                        'class' => 'update',
                        'data-id' => $model['prize_id'],
                    ]
				);
			},
            'delete' =>function ($url, $model,$key)
            {
                return Html::a('删除',$url,['title'=>'删除','class'=>'delete','data-toggle'=>false,'data-confirm'=>'确定要删除该记录吗？','data-pjax'=>'0','style'=>'margin-left:10px']);
            },
//            'detail' => function($url, $model, $key){
//                $ext_params = [];
//                if($model['type']=="直播关注活动"){
//                    $ext_params['unique_no'] = '@unique_no';
//                }
//                $url = ActivityUtil::GetMBActivityUrl($model['activity_id'], $ext_params);
//                return Html::a(
//                    '查看WEB活动页面',
//                    $url,
//                    [
//                        'target'=>'_blank'
//                    ]
//                );
//            },
		]
	]
];

?>

<style>
	.content-header{
		height: 46px;
	}
</style>

<?= GridView::widget([
        'id' => 'activityprize_list',
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => $gridColumns,
		'toolbar'=> [
	        [
	            'content'=> Html::button('新增奖品信息',['type'=>'button','title'=>'新增奖品信息', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('activityprize/create').'";return false;']),
	        ],
	        '{export}',
	        '{toggleData}',
	        'toggleDataContainer' => ['class' => 'btn-group-sm'],
	        'exportContainer' => ['class' => 'btn-group-sm']

	    ],
        'containerOptions' => ['style'=>'overflow: auto;height:650px;font-size:14px;'],
	    'pjax' => true,
	    'bordered' => true,
	    'striped' => false,
	    'condensed' => false,
	    'responsive' => true,
	    'hover' => true,
	    'exportConfig'=>['xls'=>[],'html'=>[],'pdf'=>[]],
	    'panel' => [
	        'type' => GridView::TYPE_INFO
	    ]
	]);

$js='
$("#activityprize_list-pjax").on("click",".delete",function(){
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
                    $("#activityprize_list").yiiGridView("applyFilter");
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



?>
