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
    #attentionevent-msg_type > label{
        padding-right: 20px;
        color: #333;
    }
    .form-group.has-success label{
        color: #0a0a0a;
    }
    input[type="radio"]{
        margin-right: 3px;
    }
</style>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AttentionEvent */
/* @var $form yii\widgets\ActiveForm */
/* @var $cache */
$data = [$cache['record_id']=>$cache['nick_name']];
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model,'app_id')->dropDownList($data,['disabled'=>'disabled','style'=>'width:200px']) ?>
    <hr/>
    <?php $model->msg_type = 0; ?>
    <?= $form->field($model,'msg_type')->radioList(['0'=>'文本消息','1'=>'图文消息']) ?>
    <hr/>
    <div id="list">
    <label style="color: red;font-weight: bold;">标题、内容描述、外链Url、图片Url、事件Id 消息类型为图文消息时填写！</label>
    <?= $form->field($model, 'title')->textInput() ?>
    <?= $form->field($model, 'description')->textInput() ?>
    <?= $form->field($model, 'url')->textInput() ?>
    <?= $form->field($model, 'picurl')->textInput() ?>
    <label style="color: red;font-weight: bold;">相同的事件ID会以一条消息列表展示！</label>
    <?= $form->field($model, 'event_id')->textInput() ?>
    <hr/>
    </div>
    <?= $form->field($model, 'content')->textarea(['style'=>'width:100%']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['publiclist/attention']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$js = '
$(function(){
    $("#list").hide(); 
});
$("input[type=\'radio\']").on("click",function(){
    $vue = $("input[type=radio]:checked").val();
    if($vue == 1){
        $("#list").show();
        $(".field-attentionevent-content").hide();
    }else{
        $("#list").hide();
        $(".field-attentionevent-content").show();
    }
})
';
$this->registerJs($js,\yii\web\View::POS_END);