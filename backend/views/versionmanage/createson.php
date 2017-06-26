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

    <?= $form->field($model, 'app_id')->textInput(['readonly'=>true,'style'=>'width:500px;'])->label('app标识') ?>

    <?= $form->field($model, 'module_id')->textInput(['readonly'=>false,'style'=>'width:500px;'])->label('模块id') ?>

    <?= $form->field($model, 'discribtion')->textInput(['readonly'=>false,'style'=>'width:500px;'])->label('描述')?>

    <?= $form->field($model, 'app_version_inner')->textInput(['style'=>'width:500px;'])->label('内部版本号') ?>

    <?= $form->field($model, 'link')->textInput(['style'=>'width:500px;'])->label('更新链接') ?>

    <?= $form->field($model, 'force_update')->dropDownList(['0'=>'不强制','1'=>'强制'],['style'=>'width:500px;'])->label('是否强制更新') ?>

    <?= $form->field($model, 'is_register')->dropDownList(['0'=>'否','1'=>'是'],['style'=>'width:500px;'])->label('是否重新登录') ?>

    <?= $form->field($model, 'update_content')->textarea(['style'=>'width:500px;height:200px;'])->label('更新内容')?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['versionmanage/indexson','app_id'=>$model->app_id]), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

