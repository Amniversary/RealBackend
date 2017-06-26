<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Wish */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .wish-pic
    {
        width: 300px;
    }
    .wish-pic
    {
        margin-bottom: 10px;
    }
    .wish-form
    {
        width: 600px;
    }
    .pic-del
    {
        color: #3c8dbc;
        text-decoration: none;
        font-weight: bold;
        font-size: 14px;
        line-height: 1.42857143;
        border-collapse: collapse;
        font-family: "微软雅黑", arial, sans-serif;
        margin-left: 40px;
    }
</style>
<div class="wish-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'wish_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'discribtion')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'wish_type_id')->dropDownList($type_items) ?>

    <?= $form->field($model, 'wish_type')->hiddenInput()->label(false) ?>
    <?php
    for($i = 1; $i < 7; $i ++)
    {
        $key = 'pic'.strval($i);
        ?>
        <div class="form-group field-wish-pic1 has-success">
            <label class="control-label" for="wish-pic1"><?= $model->attributeLabels()[$key] ?></label> <a class="pic-del" href="javascript:delpic('<?=$key?>')">删除</a>
            <input type="hidden" name="Wish[<?=$key?>]" id="wish_<?=$key?>" value="<?=$model->$key?>"/>
            <input class="wish-pic" type="file" class="wish-pic-file" id="pic-file-<?=$key?>"  targetctr="<?=$key?>">
            <a target="_blank" href="<?=$model->$key?>" id="a-<?=$key?>" style="<?=empty($model->$key)?'display: none;':''?>">
                <img class="wish-pic" src="<?=$model->$key?>" alt="<?= $model->attributeLabels()[$key] ?>">
            </a>
        </div>
    <?php
    }
    ?>

    <?= $form->field($model, 'publish_user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'publish_user_phone')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['wishmanage/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js = '
function delpic(targetKey)
{
    if(confirm("确定删除该图片吗"))
    {
        key = "a-" + targetKey;
        $("#" + key).hide();
        $("#" + key).attr("href", "");
        $("#" + key + " img").attr("src","");
        $("#wish_" + targetKey).val("");
    }
}
$(function(){
    $(document).on("change",".wish-pic",function(){
        //创建FormData对象
        var data = new FormData();
        //为FormData对象添加数据
        //
        hasFile = false;
        $.each($(this)[0].files, function(i, file) {
            data.append("upload_file", file);
            hasFile = true;
        });
        if(!hasFile)
        {
            return;
        }
        var targetKey = $(this).attr("targetctr");
        $.ajax({
            url:"/mypic/upload_pic?pic_type=wish",
            type:"POST",
            data:data,
            cache: false,
            contentType: false,    //不可缺
            processData: false,    //不可缺
            success:function(data)
            {
                file = $("#pic-file-"+ targetKey);
                file.after(file.clone().val(""));
                file.remove();
                data = $.parseJSON(data);
                if(data.code == "0")
                {
                    key = "a-" + targetKey;
                    $("#" + key).show();
                    $("#" + key).attr("href", data.msg);
                    $("#" + key + " img").attr("src",data.msg);
                    $("#wish_" + targetKey).val(data.msg);
                }
                else
                {
                    alert(data.msg);
                }
                console.log(data);

            },
            error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                 file = $(this);
                 file.after(file.clone().val(""));
                 file.remove();
             }
        });
    });

});
';
$this->registerJs($js,\yii\web\View::POS_END);
