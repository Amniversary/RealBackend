<style>
    .time{
        display: none;
    }
    .form-control
    {
       width: 800px;
    }
    #w1
    {
        width:800px;
        background-color: #f06e57 !important;
        border: 1px solid #f06e57 !important;
    }
    .ke-container{
        max-width: 800px;
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

    <?php if($type == 1) {?>

        <?= $form->field($model, 'user_id')->label('密播ID')->textInput()?>

        <?= $form->field($model, 'status')->label('常用语状态')->dropDownList([
            '1' => '正常',
            '2' => '禁用',
        ])?>

        <?= $form->field($model, 'content')->label('常用语文本')->textarea() ?>
        <?= $form->field($model, 'create_at',['labelOptions'=>['class'=>'control-label times']])->textInput() ?>
    <?php }elseif($type == 2) { ?>

        <?= $form->field($model, 'user_id')->label('密播ID')->textInput()?>

        <?= $form->field($model, 'status')->label('常用语状态')->dropDownList([
            '1' => '正常',
            '2' => '禁用',
        ])?>
        <?= $form->field($model, 'content')->label('常用语文本')->textarea() ?>
        <?= $form->field($model, 'create_at',['labelOptions'=>['class'=>'control-label times']])->textInput() ?>
    <?php } ?>
    <div class="time"><?php echo date('Y-m-d H:i:s') ?></div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn_1 btn btn-success' : 'btn_1 btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['commonwords/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js = '
    var time = $(".time").text()
    $("#commonwords-create_at").val(time);
    $(".field-commonwords-create_at").hide();
    $(".field-commonwords-user_id").hide();

    $(".btn_1").click(function(){
        if($("#commonwords-user_id").val() == "")
        {
            $("#commonwords-user_id").val(1);
        };
    })
';
$this->registerJs($js,\yii\web\View::POS_END);
