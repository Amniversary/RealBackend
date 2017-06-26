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
    <?= $form->field($model, 'quality')->label('标题')->textInput() ?>
    <?= $form->field($model, 'fps')->label('视频帧数（10~30）')->textInput() ?>
    <?= $form->field($model, 'video_bit_rate')->label('平均编码码率（>=300kbps）')->textInput() ?>
    <?= $form->field($model, 'width')->label('视频宽度')->textInput() ?>
    <?= $form->field($model, 'height')->label('视频高度')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['qiniuliving/living_parameters']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js = '
';
$this->registerJs($js,\yii\web\View::POS_END);
