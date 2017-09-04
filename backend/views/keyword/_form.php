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
    .user-form{
    border-radius: 5px;
        position: relative;
        border: 1px solid #d9dadc;
        background-color: #fff;
        padding: 20px;
        /*margin-bottom: 20px;*/

    }
</style>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Keywords */
/* @var $form yii\widgets\ActiveForm */
/* @var $cache */
$data = [$cache['record_id']=>$cache['nick_name']];
?>

    <div class="user-form">

        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model,'app_id')->dropDownList($data,['disabled'=>'disabled','style'=>'width:200px']) ?>
        <hr>
        <?= $form->field($model, 'rule')->dropDownList(['2'=>'模糊匹配','1'=>'精准匹配','3'=>'图片匹配'],['style'=>'width:200px']) ?>
        <hr>
        <?= $form->field($model, 'keyword')->textInput() ?>
        <br/>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['keyword/createkey']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php
$js = '
$(document).ready(function(){
    if($("#keywords-rule option:selected").val() == 3) {
        $(".field-keywords-keyword").hide();
    }else{
        $(".field-keywords-keyword").show();
    }
})
$("#keywords-rule").change(function(){
    if($("#keywords-rule option:selected").val() == 3) {
        $(".field-keywords-keyword").hide();
    }else{
        $(".field-keywords-keyword").show();
    }
})
';
$this->registerJs($js,\yii\web\View::POS_END);
