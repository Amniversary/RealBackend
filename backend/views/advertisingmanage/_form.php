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

    <?= $form->field($model, 'description')->textarea()->label('描述')?>


    <?= $form->field($model, 'start_time')->widget(\kartik\datetime\DateTimePicker::className(), [
        'language' => 'zh-CN',
        'size' => 'ms',
        'pluginOptions' => [
            'startView' => 1,
            'minView' => 0,
            'maxView' => 1,
            'autoclose' => true,
            'linkFormat' => 'HH:ii P', // if inline = true
            // 'format' => 'HH:ii P', // if inline = false
            'todayBtn' => true
        ]
    ])->label('开始时间');?>

    <?= $form->field($model, 'end_time')->widget(\kartik\datetime\DateTimePicker::className(), [
        'language' => 'zh-CN',
        'size' => 'ms',
        'pluginOptions' => [
            'autoclose' => true,
            'linkFormat' => 'HH:ii P', // if inline = true
            // 'format' => 'HH:ii P', // if inline = false
            'todayBtn' => true
        ]
    ])->label('结束时间');?>

    <?= $form->field($model, 'weights')->dropDownList(['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'])->label('权重') ?>

    <?= $form->field($model, 'link_url')->textInput(['maxlength'=>200])->label('链接地址') ?>

    <div class="form-group field-user-pic1 <?=($model->getFirstError('image_url') === null?'has-success':'has-error')?>">
        <label class="control-label" for="user-pic1">请选择图片，492*600，不得大于2M</label> <a class="pic-del" href="javascript:delpic('pic')">删除</a>
        <input type="hidden" name="AdImages[image_url]" id="user_pic" value="<?=$model->image_url?>"/>
        <input class="backend-pic-input" type="file" class="user-pic-file" id="pic-file-pic"  targetctr="pic">
        <a target="_blank" href="#" id="a-pic" style="<?=empty($model->image_url)?'display: none;':''?>">
            <img class="user-pic" src="<?=$model->image_url?>" alt="图像">
        </a>
        <div class="help-block"><?=$model->getFirstError('image_url')?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['advertisingmanage/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
