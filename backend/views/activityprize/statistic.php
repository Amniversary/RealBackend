<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use frontend\business\ActivityUtil;

$this->title = '抽奖统计信息';
//$this->params['breadcrumbs'][] = ['label'=>'蜜播活动管理', 'url'=>['index']];
//$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
	['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'title',
        'vAlign' => 'middle',
        'label' => '活动名称',
    ],
    [
        'attribute' => 'user_number',
        'vAlign' => 'middle',
        'label' => '注册人数',
    ],
    [
        'attribute' => 'record_number',
        'vAlign' => 'middle',
        'label' => '抽奖次数',
    ],
    [
        'attribute' => 'record_number',
        'vAlign' => 'middle',
        'label' => '中奖次数',
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
		'toolbar'=> [[],
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
