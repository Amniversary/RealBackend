<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

if(is_null($model->activity_id)){
	$this->title = '新增活动';
}else{
	$this->title = '编辑活动';
}

$this->params['breadcrumbs'][] = ['label'=>'蜜播活动管理', 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label'=>'活动设置', 'url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;

$dropDownList = array();

foreach ($templates as $tp) {
    $template_id = $tp['template_id'];
    $dropDownList[$template_id] = $template_id;
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
					<?= $form->field($model, 'title')?>
                    <?= $form->field($model, 'start_time',['labelOptions'=>['class'=>'control-label']])->label('起始时间')->widget(\yii\jui\DatePicker::classname(), [
                        'language' => 'zh-CN',
                        'dateFormat' => 'yyyy-MM-dd',
                        'options' => ['class'=>'form-control']
                    ]) ?>
                    <?= $form->field($model, 'end_time',['labelOptions'=>['class'=>'control-label']])->label('结束时间')->widget(\yii\jui\DatePicker::classname(), [
                        'language' => 'zh-CN',
                        'dateFormat' => 'yyyy-MM-dd',
                        'options' => ['class'=>'form-control']
                    ]) ?>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'status')->dropDownList(['2'=>'进行中','1'=>'未开始','0'=>'结束']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'type')->dropDownList($activity_type) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'template_id')->dropDownList(\backend\business\ScoreGiftUtil::GetOtherActivityTemplate()); ?>
                        </div>
                    </div>
                </div>
				<div class="box-footer">
					<?php if(is_null($model->activity_id)){ ?>
						<?= Html::submitButton('新增',['class' => 'btn btn-primary']) ?>
					<?php }else{ ?>
						<?= Html::submitButton('编辑',['class' => 'btn btn-primary']) ?>
					<?php } ?>
					
					<a href="index" class="btn btn-primary">取消</a>
				</div>

			<?php ActiveForm::end(); ?>
		</div>
	</div>	
</div>
