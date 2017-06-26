<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GetCashSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="get-cash-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'get_cash_id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'nick_name') ?>

    <?= $form->field($model, 'cash_money') ?>

    <?= $form->field($model, 'cash_rate') ?>

    <?php // echo $form->field($model, 'cash_fees') ?>

    <?php // echo $form->field($model, 'real_cash_money') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'refuesd_reason') ?>

    <?php // echo $form->field($model, 'finance_remark') ?>

    <?php // echo $form->field($model, 'identity_no') ?>

    <?php // echo $form->field($model, 'real_name') ?>

    <?php // echo $form->field($model, 'card_no') ?>

    <?php // echo $form->field($model, 'bank_name') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'check_time') ?>

    <?php // echo $form->field($model, 'finace_ok_time') ?>

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
