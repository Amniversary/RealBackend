<style>
    body
    {
        background-color: #F4F4F4;
    }
    .fom{
        line-height: 48px;
        text-align: center;
        background-color: #ff5757;
        color: #fff;
        height: 48px;
        font-size: 22px;
        vertical-align: middle;
    }


    .fom a{
        text-decoration: none;
        color: #fff;
        position: absolute;
        left: 20px;
    }


    .ign{
        display: block;
        margin: 0 auto;
        width: 86px;
        height: 86px;
        margin: 22px auto 30px auto;
        border: 1px solid #A5A6AC;
        border-radius: 50%;
    }

    form#login-form {
        display: block;
        margin: 0 15px;
    }

    .form-group {
        display: block;
        border: 1px solid #E8E8E8;
        border-radius: 5px;
        padding: 10px;
        line-height: 30px;
        height: 30px;
        margin-top: 10px;
        background-color: #F9F9F8;
    }

.form-control
{
    background-color: #F9F9F8;
}

    input#weixinloginform-phone_no {
        border: 0px;
        font-size: 14px;
        outline:none;
        width: 150px;
        color:  #C2C2C2;
    }


    input#weixinloginform-vcode{
        border: 0px;
        font-size: 14px;
        border: none;
        width: 116px;
        color:  #C2C2C2;
    }

    button#send_vcode {
        background-color: #fff;
        outline: none;
        border: 0;
        border-left: 1px solid #ff5757;
        font-size: 14px;
        color: #ff5757;
        background-color: #F9F9F8;
        float: right;
        padding: 0 0 0 8px;
        margin: 7px 0;
        width: 79px;
    }

.form-group-bottom
{
    margin: 60px 0px 30px 0px;
}

.protocal
{
    margin-top: 20px;
    vertical-align: middle;
}
    .agri
    {
        width: auto;
        height: auto;
        color: #2F2F2F;
        margin-left: 8px;
        font-size:14px;
    }
    .check-img
    {
        width: 20px;
        height: 20px;
        vertical-align: middle;
    }
    .agri_a
    {
        text-decoration: none;
        color:#FE6969;
        font-size: 14px;
    }
    .error-summary
    {
        margin: 0px;
    }
    .error-summary ul
    {
        margin: 0px;
    }
    .btn-primary
    {
        border-radius: 5px;
        width: 100%;
        height: 50px;
        font-size: 25px;
        margin: 5px 0px;
        border: none;
        background-color: #FF5757;
        color: #FFF9F9;
    }
</style>



<?php
use \yii\bootstrap\ActiveForm;
?>
<div class="fom">
    <a href="<?=$back_url?>">取消</a><span>登录</span>
</div>
<img src="http://image.matewish.cn/wish_web/person-3.png" class="ign"/>

<?php $form = ActiveForm::begin(['id' => 'login-form','method'=>'POST']); ?>

    <div class="form-group">
        <label class="control-label" for="weixinloginform-phone_no">手机号</label>
        <input type="text" value="<?=$model->phone_no?>"  placeholder="请输入手机号码" id="weixinloginform-phone_no" class="form-control" name="WeiXinLoginForm[phone_no]">
    </div>
<div class="form-group">
    <label class="control-label" for="weixinloginform-vcode">验证码</label>
    <input type="text" value="<?=$model->vcode?>" placeholder="请输入验证码" id="weixinloginform-vcode" class="form-control" name="WeiXinLoginForm[vcode]">
    <button id="send_vcode">获取验证码</button>
</div>
<div class="protocal">
<?php
echo \yii\helpers\Html::hiddenInput('protocal_id','1',['id'=>'protocal_id']);
echo \yii\bootstrap\Html::img('http://image.matewish.cn/frontmywish/protocal_choose.png',['id'=>'img_check','alt'=>'同意协议','class'=>'check-img']).\yii\bootstrap\Html::label('我已阅读并同意','checkbox-1-1',['class'=>'agri']
    ).\yii\bootstrap\Html::a('《美愿用户使用协议》','/protocal.html',['class'=>'agri_a']
    )
?>
</div>
<!-- <?= $form->field($model, 'phone_no')->textInput() ?> -->

<!-- <?= $form->field($model, 'vcode')->textInput() ?> -->
<?= $form->errorSummary($model)?>
<div class="form-group-bottom">
    <?= \yii\helpers\Html::submitButton('登录', ['class' => 'btn btn-primary', 'id'=>'login-button','name' => 'login-button']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$js = '
var isSendVcode = false;
var timerHander = null;
var leftTime = 59;
function setSecondText()
{
    if(leftTime <= 0)
    {
        stopTimer();
        return;
    }
    leftTime --;
    var $txt = (leftTime.toString().length < 2 ? "0" + leftTime.toString():leftTime.toString());
    $("#send_vcode").html($txt);
}

function stopTimer()
{
    if(timerHander != null)
    {
        clearInterval(timerHander);
        timerHander = null;
        leftTime = 59;
        $("#send_vcode").html("获取验证码");
        $("#send_vcode").removeAttr("disabled");
    }
}


$("#send_vcode").on("click",function() {
    var phoneNum = $("#weixinloginform-phone_no").val();
    if(phoneNum  == null || phoneNum.length != 11)
    {
        $(".error-summary").show();
        if($(".error-summary ul").html().length == 0)
        {
            $(".error-summary ul").html("手机号不能为空");
        }
        return false;
    }
    else
    {
        $(".error-summary").hide();
        if($(".error-summary ul").html().length > 0)
        {
            $(".error-summary ul").html("");
        }
    }
    $(this).attr("disabled",true);
    timerHander = setInterval("setSecondText()", 1000);
    $.ajax({
        type: "POST",
        url: "/mywish/getvcode?token='.$token.'",
        data: "phone_no="+phoneNum,
        success: function(data)
        {
            data = $.parseJSON(data);
/*            if(data.code != "0")
            {
                stopTimer();
                alert(data.msg);
            }*/
        },
        error: function (XMLHttpRequest, textStatus, errorThrown)
        {
            //document.write(XMLHttpRequest.responseText);
            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
            //$("#send_vcode").removeAttr("disabled");
         }
    });
    return false;
});

$("#img_check").click(function(){
    check = $("#protocal_id").val();
    if(check == "1")
    {
        $(this).attr("src","http://image.matewish.cn/frontmywish/protocal_unchoose.png");
        $("#protocal_id").val("0");
    }
    else
    {
        $(this).attr("src","http://image.matewish.cn/frontmywish/protocal_choose.png");
        $("#protocal_id").val("1");
    }
});

$("#login-button").on("click",function(){
    check = $("#protocal_id").val();
    if(check != "1")
    {
        return false;
    }
    return true;
});

';
$this->registerJs($js,\yii\web\View::POS_END);