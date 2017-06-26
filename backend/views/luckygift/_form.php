<style>
    .user-pic
    {
        width: 80px;
        height: 80px;
    }
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
    <?= $form->field($model, 'receive_rate')->textInput(['placeholder'=>'%'])->label('主播收到的票数比例（%）') ?>
    <?= $form->field($model, 'basic_beans')->textInput()->label('基本豆（当送的礼物豆值大于等于基本豆主播才有机率获得幸运礼物）') ?>
    <?= $form->field($model, 'multiple')->textInput() ?>
    <?= $form->field($model, 'rate')->textInput(['placeholder'=>'%'])->label('概率（%）') ?>
    <?= $form->field($model, 'status')->dropDownList(['0'=>'否','1'=>'是']) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['luckygift/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs($js,\yii\web\View::POS_END);
