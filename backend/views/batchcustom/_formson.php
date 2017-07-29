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
    #authorizationmenuson-type > label{
        padding-right: 20px;
        color: #333;
    }
    input[type="radio"]{
        margin-right: 3px;
    }
</style>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AuthorizationMenuSon */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="user-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name')->textInput(['style'=>'width:200px']) ?>
        <hr>
        <div id="type">
            <?= $form->field($model, 'type')->radioList(['view'=>'跳转链接','click'=>'点击事件'],['class'=>'event','style'=>"width:200px"]) ?>
        <hr>
        </div>
        <div id="event">
            <label style="color: red" >点击事件回调的EventKey</label>
            <?= $form->field($model, 'key_type')->textInput() ?>
        </div>
        <div id="url">
            <?= $form->field($model, 'url')->textInput() ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['batchcustom/indexson','menu_id'=>$menu_id,'id'=>$id]), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
<?php
$js = '
$(function(){
    $vue = $("#authorizationmenuson-type input[type=\'radio\']:checked").val()
    if($vue == "view"){
        $("#url").show();
        $("#event").hide();
    }else{
        $("#url").hide();
        $("#event").show();
    }
})
$("#authorizationmenuson-type input[type=\'radio\']").on("click",function(){
    $vue = $("#authorizationmenuson-type input[type=\'radio\']:checked").val()
    if($vue == "view"){
        $("#url").show();
        $("#event").hide();
    }else{
        $("#url").hide();
        $("#event").show();
    }
})
';
$this->registerJs($js,\yii\web\View::POS_END);
