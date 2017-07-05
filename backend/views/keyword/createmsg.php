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
    #attentionevent-content{
        height: 200px;
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
    <?= $form->field($model,'msg_type')->radioList(['0'=>'文本消息','1'=>'图文消息']) ?>
    <hr/>
    <?= $form->field($model,'key_id')->dropDownList(\common\models\Keywords::getKeyWord($cache['record_id']),['style'=>'width:200px']) ?>
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
    <div id="content-msg">
    <label style="color: red;font-weight: bold;">1.超链接中 href 为链接Url，例：< a href="http://wxmp.gatao.cn">Real后台< /a><br/>
    2.回车即代表换行</label>
    <?= $form->field($model, 'content')->textarea() ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['keyword/keyword']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::button('添加超链接标签', ['id'=>'super-link','class' =>'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$js = '
$(function(){
    $vue = $("input[type=radio]:checked").val();
    if($vue == 0){
        $("#list").hide();
        $("#super-link").show();
    }else{
        $("#content-msg").hide();
        $("#super-link").hide();
    }
});
$("input[type=\'radio\']").on("click",function(){
    $vue = $("input[type=radio]:checked").val();
    if($vue == 1){
        $("#list").show();
        $("#super-link").hide();
        $("#content-msg").hide();
    }else{
        $("#list").hide();
        $("#super-link").show();
        $("#content-msg").show();
    }
});
$("#super-link").on("click",function(){
    $text = $("#attentionevent-content").val();
    $("#attentionevent-content").val($text + "<a href=\"\"></a>");
})
';
$this->registerJs($js,\yii\web\View::POS_END);