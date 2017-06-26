<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RedPackets */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="red-packets-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'packets_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'packets_money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'get_type')->textInput() ?>

    <?= $form->field($model, 'overtime_days')->textInput() ?>

    <?= $form->field($model, 'start_time')->textInput() ?>

    <?= $form->field($model, 'end_time')->textInput() ?>

    <?= $form->field($model, 'packets_type')->textInput() ?>

    <?= $form->field($model, 'remark1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark4')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
