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
    .form-control{
        width: 600px;
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

    <?= $form->field($model, 'gift_order')->textInput() ?>
    <?= $form->field($model, 'gift_name')->textInput() ?>
    <?= $form->field($model, 'gift_money')->textInput() ?>
    <?= $form->field($model, 'gift_integral')->textInput() ?>
    <?= $form->field($model, 'gift_num')->textInput() ?>
    <?= $form->field($model, 'gift_send_num')->textInput() ?>
    <?= $form->field($model, 'gift_details')->textarea() ?>
    <?= $form->field($model, 'gift_grant')->textarea() ?>
    <?= $form->field($model, 'gift_accept')->textarea() ?>
    <?= $form->field($model, 'gift_type')->dropDownList([
        '1' => '官方虚拟物品-游戏币',
        '2' => '官方虚拟物品-鲜花',
        '3' => '官方虚拟物品-经验值',
        '200' => '虚拟物品',
        '300' => '红包',
        '400' => '实际物品'
    ])?>

    <div class="form-group field-user-pic1 <?=($model->getFirstError('gift_pic') === null?'has-success':'has-error')?>">
        <label class="control-label" for="user-pic1">礼品图片</label> <a class="pic-del" href="javascript:delpic('pic')">删除</a>
        <input type="hidden" name="IntegralMall[gift_pic]" id="user_pic" value="<?=$model->gift_pic?>"/>
        <input class="backend-pic-input" type="file" class="user-pic-file" id="pic-file-pic"  targetctr="pic">
        <a target="_blank" href="#" id="a-pic" style="<?=empty($model->gift_pic)?'display: none;':''?>">
            <img class="user-pic" src="<?=$model->gift_pic?>" alt="图像">
        </a>
        <div class="help-block"><?=$model->getFirstError('gift_pic')?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['integralmall/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
        if(sourceUrl == "http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/person-1.png")
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
            if(file.size < 500000)
            {
                data.append("upload_file", file);
                hasFile = true;
            }
            else{
                alert("图片的大小不能大于500KB");
            }
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
