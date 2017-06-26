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

    <?= $form->field($model, 'group_id')->dropDownList(['1'=>'用户设备参数','2'=>'分享信息参数','3'=>'签到打赏参数','4'=>'费率信息参数','5'=>'统计信息参数','6'=>'直播参数设置','7'=>'心跳参数设置']) ?>

    <?= $form->field($model, 'code')->textInput()?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'description')->textInput() ?>

    <?= $form->field($model, 'value1')->textInput() ?>

    <?= $form->field($model, 'value2')->textInput() ?>

    <?= $form->field($model, 'value3')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['updatemanage/user_device_params']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

