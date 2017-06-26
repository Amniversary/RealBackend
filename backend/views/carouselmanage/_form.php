<style>
    .user-pic
    {
/*        width: 80px;
        height: 80px;*/
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

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'discribtion')->textarea()?>

    <?= $form->field($model, 'action_type')->dropDownList(['0'=>'无','1'=>'搜索','2'=>'愿望跳转','3'=>'网页链接','4' => '下载app链接']) ?>

    <?= $form->field($model, 'action_content')->textarea()->hint('可以为空')?>

    <?= $form->field($model, 'order_no')->textInput(['maxlength'=>4]) ?>

    <?= $form->field($model, 'activity_type')->dropDownList($activity_type) ?>

    <div class="form-group field-user-pic1 <?=($model->getFirstError('pic_url') === null?'has-success':'has-error')?>">
        <label class="control-label" for="user-pic1">图片(建议图片大小640*200)</label> <a class="pic-del" href="javascript:delpic('pic')">删除</a>
        <input type="hidden" name="Carousel[pic_url]" id="user_pic" value="<?=$model->pic_url?>"/>
        <input class="backend-pic-input" type="file" class="user-pic-file" id="pic-file-pic"  targetctr="pic">
        <a target="_blank" href="#" id="a-pic" style="<?=empty($model->pic_url)?'display: none;':''?>">
            <img class="user-pic" src="<?=$model->pic_url?>" alt="图像">
        </a>
        <div class="help-block"><?=$model->getFirstError('pic_url')?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['carouselmanage/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
        sourceUrl = $("#" + key).attr("href");
        if(sourceUrl == "http://image.matewish.cn/wish_web/person-1.png")
        {
            return;
        }
        $("#" + key).hide();
        $("#" + key).attr("href", "");
        $("#" + key + " img").attr("src","");
        $("#user_" + targetKey).val("");
    }
}
$(function(){
    $(document).on("change",".backend-pic-input",function(){
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
            url:"/mypic/upload_pic?pic_type=back_user",
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
                    $("#user_" + targetKey).val(data.msg);
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
