<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

if(is_null($model->template_id)){
	$this->title = '新增活动模板';
}else{
	$this->title = '编辑活动模板';
}

$this->params['breadcrumbs'][] = ['label'=>'礼物积分管理', 'url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;
$activity_type_one = array_shift($activity_type);


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
					<?= $form->field($model, 'template_title')->label('活动模板')?>
                    <?= $form->field($model, 'template_type')->label('活动类型')->dropDownList($activity_type)?>
					<?= $form->field($model, 'file_name')->label('活动文件名') ?>
				</div>	
				<div class="box-footer">
					<?php if(is_null($model->template_id)){ ?>
						<?= Html::submitButton('新增',['class' => 'btn btn-primary']) ?>
					<?php }else{ ?>
						<?= Html::submitButton('编辑',['class' => 'btn btn-primary']) ?>
					<?php } ?>
					
					<a href="/activitytemplate/index" class="btn btn-primary">取消</a>
				</div>

			<?php ActiveForm::end(); ?>
		</div>
	</div>	
</div>
