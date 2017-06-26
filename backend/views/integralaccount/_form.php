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
    <?= $form->field($model, 'integral_account_balance')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '充值', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['integralaccount/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>