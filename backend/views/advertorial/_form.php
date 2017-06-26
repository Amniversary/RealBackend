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
    .time{
        display: none;
    }
    .form-control{
        width: 1100px;
    }
</style>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'advertorial_title')->label('软文标题')->textInput() ?>

    <?= $form->field($model, 'create_time',['labelOptions'=>['class'=>'control-label times']])->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'zh-CN',
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class'=>'form-control']
    ]) ?>
    <div class="time"><?php echo date('Y-m-d H:i:s') ?></div>

    <?= $form->field($model, 'advertorial_content')->label('软文内容')->widget('\pjkui\kindeditor\KindEditor',
        ['clientOptions'=>
            [
                'allowFileManager'=>'true',
                'allowUpload'=>'true',
                'urlType' => 'domain',
                'width' => '1100',
                'uploadJson' => '/advertorial/kupload?action=uploadJson',
                'items' => [
                    'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                    'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                    'insertunorderedlist', '|', 'image'
                ]
            ]
        ])
    ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '保存' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['advertorial/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js = '
      var time = $(".time").text()
      $("#advertorial-create_time").val(time);
      $("#advertorial-create_time").hide();
      $(".times").hide();
';
$this->registerJs($js,\yii\web\View::POS_END);
