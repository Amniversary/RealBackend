<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

if(is_null($model->level_id)){
	$this->title = '新增等级';
}else{
	$this->title = '编辑等级';
}

$this->params['breadcrumbs'][] = ['label'=>'等级信息', 'url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;

$dropDownList = array();

foreach ($levelStage as $ls) {
	$level_stage = $ls['level_stage'];
  	$dropDownList[$level_stage] = $level_stage;
}

?>

<style>
	.content-header{
		height: 46px;
	}
</style>

<div class="row">
	<div class="col-md-6">
		<div class="box box-primary">
			<?php 
				$form = ActiveForm::begin(['id' => 'login-form','class' => 'form']); 
			?>
				<div class="box-body">
					<?= $form->field($model, 'level_name')->label('等级名称'); ?>
					<?= $form->field($model, 'experience')->label('经验'); ?>
					<div class="row">
						<div class="col-md-3">
							<?= $form->field($model, 'level_max')->dropDownList($dropDownList); ?>		
						</div>
					</div>	
				</div>	
				<div class="box-footer">
					<?php if(is_null($model->level_id)){ ?>
						<?= Html::submitButton('新增',['class' => 'btn btn-primary']) ?>
					<?php }else{ ?>
						<?= Html::submitButton('编辑',['class' => 'btn btn-primary']) ?>
					<?php } ?>
					
					<a href="/levelmanage/index" class="btn btn-primary">取消</a>
				</div>

			<?php ActiveForm::end(); ?>
		</div>
	</div>	
</div>
