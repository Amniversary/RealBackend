<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\RedPacketsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="red-packets-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'red_packets_id') ?>

    <?= $form->field($model, 'packets_name') ?>

    <?= $form->field($model, 'discribtion') ?>

    <?= $form->field($model, 'pic') ?>

    <?= $form->field($model, 'packets_money') ?>

    <?php // echo $form->field($model, 'get_type') ?>

    <?php // echo $form->field($model, 'overtime_days') ?>

    <?php // echo $form->field($model, 'start_time') ?>

    <?php // echo $form->field($model, 'end_time') ?>

    <?php // echo $form->field($model, 'open_type') ?>

    <?php // echo $form->field($model, 'packets_type') ?>

    <?php // echo $form->field($model, 'remark1') ?>

    <?php // echo $form->field($model, 'remark2') ?>

    <?php // echo $form->field($model, 'remark3') ?>

    <?php // echo $form->field($model, 'remark4') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
