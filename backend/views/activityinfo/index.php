<?php
	
use kartik\grid\GridView;
use yii\helpers\Html;
use frontend\business\ActivityUtil;

$this->title = '活动设置';
$this->params['breadcrumbs'][] = ['label'=>'蜜播活动管理', 'url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['acitvity_types'] = $activity_type;
$gridColumns = [
	['class' => 'kartik\grid\SerialColumn'],
	[
        'attribute' => 'title',
        'vAlign' => 'middle',
        'label' => '活动标题'
    ],
	[
		'attribute' => 'start_time',
        'vAlign' => 'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
        'label' => '开始时间'
	],
    [
        'attribute' => 'end_time',
        'vAlign' => 'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
        'label' => '结束时间'
    ],
    [
        'attribute' => 'status',
        'vAlign' => 'middle',
        'value' => function($model)
        {
            switch($model['status']){
                case '0':
                    $model['status'] = "已结束";
                    break;
                case '1':
                    $model['status'] = "未开始";
                    break;
                case '2':
                    $model['status'] = "进行中";
                    break;
            }
            return $model['status'];
        },
        'filter'=>['0'=>'已结束','1'=>'未开始','2'=>'进行中'],
        'label' => '活动状态'
    ],
    [
        'attribute' => 'type',
        'vAlign' => 'middle',
        'value' => function($model)
        {

            return $this->params['acitvity_types'][$model['type']];
        },
        'filter'=>$activity_type,
        'label' => '活动类型'
    ],
    [
        'attribute' => 'create_time',
        'vAlign' => 'middle',
        'filterType'=>'\yii\jui\DatePicker',
        'filterWidgetOptions'=>[
            'language'=>'zh-CN',
            'dateFormat'=>'yyyy-MM-dd',
            'options'=>['class'=>'form-control','style'=>'display:inline-block;']
        ],
        'label' => '创建时间'
    ],
    [
        'attribute' => 'template_id',
        'vAlign'=>'middle',
        'label'=>'模板类型',
        'filter'=> \backend\business\ScoreGiftUtil::GetOtherActivityTemplate(),
        'value'=>function($model)
        {
            switch($model['template_id'])
            {
                case $model['template_id']:
                    $model['template_id'] = $model['template_title'];
                    break;
            }
            return $model['template_id'];
        }
    ],
	[
		'class' => 'kartik\grid\ActionColumn',
		'header' => '操作',
		'template' => '{update}&emsp;&emsp;{delete}&emsp;&emsp;{detail}',
		'width' => '280px',
		'buttons' => [
			'update' => function($url, $model, $key){
				return Html::a(
					'编辑',
					['update', 'id' => $model['activity_id']],
                    [
                        'class' => 'update',
                        'data-id' => $model['activity_id'],
                        'data-status' => $model['status'],
                    ]
				);
			},
			'delete' => function($url, $model, $key){
				return Html::a(
					'删除',
					['delete', 'id' => $model['activity_id']],
					[
						'class' => 'delete',
                        'data-id' => $model['activity_id'],
                        'data-status' => $model['status'],
					]
				);
			},
            'detail' => function($url, $model, $key){
                $ext_params = [];
                if($model['type']=='3')
                {
		            $ext_params['unique_no'] = '@unique_new';
                }
                if($model['type']=='2')
                {
                    $ext_params['unique_no'] = '@unique_no';
                }
                //\Yii::getLogger()->log('activity_info_model:'.var_export($model,true),\yii\log\Logger::LEVEL_ERROR);
                $url = ActivityUtil::GetMBActivityUrl($model['activity_id'], $ext_params);
                return Html::a(
                    '查看WEB活动页面',
                    $url,
                    [
                        'target'=>'_blank'
                    ]
                );
            },
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
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => $gridColumns,
		'toolbar'=> [
	        [
	            'content'=> Html::button('新增蜜播活动',['type'=>'button','title'=>'新增蜜播活动', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('activityinfo/create').'";return false;']),
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
	]) 
?>
<?php
$js='
    //点击删除
    $(document).on("click", ".delete", function(){
        var status = $(this).data("status");
        if(status == 1){
            //活动未开始,可删除
            confirm("你确定要删除改活动吗？");
        }else{
            alert("已开始或者结束的活动不允许删除哦");
            return false;
        }
    });
    //点击编辑
    $(document).on("click", ".update", function(){
        var status = $(this).data("status");
        if(status == 0){
            //活动已结束,不可编辑
            alert("已结束的活动不能编辑哦");
            return false;
        }
    });
';
$this->registerJs($js,\yii\web\View::POS_END);