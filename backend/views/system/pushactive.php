<style>
    .backend-pic-input
    {
        margin-bottom: 10px;
    }
    .cnt
    {
        width: 100%;
        height: 150px;
    }
</style>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

\common\assets\ArtDialogAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['options'=>[]]); ?>

    <?= $form->field($model, 'message')->textarea(['class'=>'cnt'])?>

    <div class="form-group">
        <?= Html::submitButton('发送', ['id'=>'send','class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js='
var dialog=null;
artDialog.tips = function (content, time) {
    return artDialog({
        id: "Tips",
        title: false,
        cancel: false,
        fixed: true,
        lock: true
    })
    .content("<div style=\"padding: 0 1em;\">" + content + "</div>")
    .time(time || 1);
};
$("#send").on("click",function(){
        msg = $("#pushmessageform-message").val();
        if(msg.length < 15)
        {
            artDialog.tips("内容不能小于15个字符");
            return false;
        }
        data="message=" + msg;
        dialog = art.dialog({
        title:"正在发送",
        fixed:true,
        lock:true,
        content:"<img src=\"http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/loading24.gif\" />",
        ok:false
    });
        $.ajax({
            url:"/system/pushactive",
            type:"POST",
            data:data,
            success:function(data)
            {
                if(data.length > 200)
                {
                    artDialog.tips("系统内部错误");
                    if(dialog != null)
                    {
                        dialog.close();
                    }
                    return false;
                }
                data = $.parseJSON(data);
                if(data.code == "0")
                {
                    artDialog.tips("发送成功");
                }
                else
                {
                    artDialog.tips(data.msg);
                }
                if(dialog != null)
                {
                    dialog.close();
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                artDialog.tips("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                //alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                if(dialog != null)
                {
                    dialog.close();
                }
             }
        });
        return false;
});
';
$this->registerJs($js,\yii\web\View::POS_END);
?>