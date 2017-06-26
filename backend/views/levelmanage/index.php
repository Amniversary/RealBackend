<?php
	
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = '等级管理';
$this->params['breadcrumbs'][] = ['label'=>'等级信息', 'url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
	['class' => 'kartik\grid\SerialColumn'],
	[
		'attribute' => 'level_name',
		'width' => '30%'
	],
	'experience',	
	[
		'attribute' => 'level_max',
		'width' => '30%'
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
						'data' => ['confirm' => '你确定要删除该记录嘛？']
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
	            'content'=> Html::button('新增等级',['type'=>'button','title'=>'新增等级', 'class'=>'btn btn-success', 'onclick'=>'location="'.\Yii::$app->urlManager->createUrl('levelmanage/create').'";return false;']),
	        ],
	        '{export}',
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
	    'panel' => [
	        'type' => GridView::TYPE_INFO
	    ]
	]) 
?>