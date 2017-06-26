<style>
    .backend-pic-input
    {
        margin-bottom: 10px;
    }
</style>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'module_id')->textInput(['readonly'=>true]) ?>

    <?= $form->field($model, 'discribtion')->textInput(['readonly'=>true])?>

    <?= $form->field($model, 'version')->textInput() ?>

    <?= $form->field($model, 'inner_version')->textInput() ?>

    <?= $form->field($model, 'link')->textInput() ?>

    <?= $form->field($model, 'force_update')->dropDownList(['0'=>'不强制','1'=>'强制']) ?>



    <?= $form->field($model, 'update_content')->textarea()?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
    </div>

    <?php ActiveForm::end(); ?>

</div>

