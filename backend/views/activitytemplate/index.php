<?php
	
use kartik\grid\GridView;
use yii\helpers\Html;

//$this->title = '活动模板管理';
//$this->params['breadcrumbs'][] = ['label'=>'礼物积分管理', 'url'=>['index']];
//$this->params['breadcrumbs'][] = $this->title;
$activity_type_one = array_shift($activity_type);
$this->params['acitvity_types'] = $activity_type;


$gridColumns = [
	['class' => 'kartik\grid\SerialColumn'],
	[
        'attribute' => 'template_id',
        'vAlign' => 'middle',
        'label' => '活动ID',
    ],
	[
		'attribute' => 'template_title',
		'width' => '30%',
        'vAlign' => 'middle',
        'label' => '活动模板',
	],
    [
        'attribute' => 'template_type',
        'vAlign' => 'middle',
        'label' => '活动类型',
        'value' => function($model)
        {
            return $this->params['acitvity_types'][$model['template_type']];
        },
        'filter'=>$activity_type
    ],
    [
        'attribute' => 'file_name',
        'vAlign' => 'middle',
        'label' => '活动文件名',
    ],
	[
		'class' => 'kartik\grid\ActionColumn',
		'header' => '操作',
		'template' => '{update}&emsp;&emsp;{delete}',
		'width' => '200px',
		'buttons' => [
			'update' => function($url, $model, $key){
				return Html::a(
					'编辑',
					['update', 'id' => $key]
				);
			},
			'delete' => function($url, $model, $key){
				return Html::a(
					'删除',
					['delete', 'id' => $key],
					[
						'data' => ['confirm' => '你确定要删除该活动模板嘛？']
					]
				);
			},
		]
	]
];

?>

<style>
	/*.content-header{*/
		/*height: 46px;*/
	/*}*/
    #w0{
        background-color: red !important;
        border: 1px solid red !important;
    }
</style>

<?php
    echo \yii\bootstrap\Alert::widget([
        'body'=>'该菜单只允许开发人员设置。',
        'options'=>[
            'class'=>'alert-info',
        ]
    ]);
?>


<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => $gridColumns,
		'toolbar'=> [
	        [
	            'content'=> Html::button('新增活动模板',['type'=>'button','title'=>'新增活动模板', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('activitytemplate/create').'";return false;']),
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