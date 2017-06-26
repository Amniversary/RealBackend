<?php
use \yii\helpers\Html;
\common\assets\ArtDialogAsset::register($this);
?>
<style>
    body
    {
        background-color: #f4f4f4;
        font-family: "微软雅黑", arial, sans-serif;
    }
    a
    {
        text-decoration: none;
    }
    .header
    {
        height: 50px;
        background-color: #ff5757;
        font-size: 18px;
        text-align: center;
        line-height: 50px;
    }
    .header span
    {
        float: left;
        margin-left: 5px;
    }
    .header a
    {
        color: #fff;
        margin-right: 30px;
    }
    .user-info
    {
        height: 80px;
        border-bottom: 1px solid #ebebeb;
        background-color: #fff;

    }
    .reward-input
    {
        margin-top: 10px;
        height: 50px;
        background-color: #fff;
        border-top: 1px solid #ebebeb;
        border-bottom: 1px solid #ebebeb;
        line-height: 50px;
    }
    .font-margin
    {
        margin-left: 15px;
    }
    .reward-txt
    {
        float: right;
    }
    .reward-txt input
    {
        border: none;
        outline: none;
    }
    #reward_money
    {
        width: 100px;
    }
    .payment
    {
        height: 45px;
        line-height: 45px;
    }
    .payment-else
    {
        height: 50px;
        background-color: #fff;
        border-top: 1px solid #ebebeb;
        line-height: 50px;

    }
    .pay-address
    {
        margin-top: 10px;
        height: 50px;
        background-color: #fff;
        border-top: 1px solid #ebebeb;
        border-bottom: 1px solid #ebebeb;
        line-height: 50px;
    }
    .pay-address label
    {
        margin-left: 15px;
    }
    .pay-address input
    {
        margin-left: 5px;
        border: none;
    }
    #remark2
    {
        height: 40px;
        border-radius: 3px;
        font-size: 14px;
        border:none;
        outline: none;
        width: 100%;
    }
    .footer
    {
        position:fixed;
        bottom: 0px;
        width: 100%;
        text-align: center;
    }
    .footer button
    {
        color: #fff;
        background-color: #ff5757;
        border-radius: 3px;
        border: none;
        width: 100px;
        height: 40px;
        font-size: 16px;
        vertical-align: middle;
        cursor: pointer;
    }
    .footer span
    {
        color: #9a9a9a;
    }
    .pay-button
    {
        width: 100%;
        border-top: 1px solid #ebebeb;
        background-color: #fff;
        line-height: 65px;
        margin-top: 10px;
    }
    .footer-contain
    {
        margin: 0 10px;
        text-align: left;

    }
    .footer-left
    {
        display: inline-block;
        margin-right: 8px;
        overflow: hidden;
        border: 1px solid #ebebeb;
        height: 40px;
        vertical-align: middle;
        line-height: 40px;
        border-radius: 3px;
    }
    .footer-right
    {
        float: right;
    }
    .head-item
    {
        vertical-align: top;
        padding-top: 7px;
    }

    .head-left
    {
        width: 62px;
        height: 62px;
        display: inline-block;
        margin: 8px 6px 8px 15px;
        border-radius: 5px;
        float: left;
    }

    .left-days
    {
        clear: right;
        float: right;
        margin-right: 10px;
    }
    .user-info .user-pic-sex
    {
        width: 15px;
        height: 15px;
        margin: 0px;
        vertical-align: middle;
        margin-left: 5px;
    }
    .user-info .user-pic-user
    {
        width: 30px;
        height: 30px;
        margin: 0px;
        vertical-align: middle;
        border-radius: 50%;
    }
    .user-name
    {
        vertical-align: middle;
        margin-left: 7px;
        font-size: 16px;
    }
    .right-up
    {
        text-align: left;
        vertical-align: middle;
    }
    .right-middle
    {
        font-size: 14px;
        color: #9a9a9a;
    }
    .right-bottom
    {
        font-size: 14px;
    }
    #choose_address
    {
        text-decoration: none;
        float: right;
        margin-right: 20px;
        color: #D9D9D9;
        font-size: 30px;
    }
    .check-item
    {
        float: right;
        width: 20px;
        height: 20px;
    }
    .check-item-checed
    {
        background-image: url("http://image.matewish.cn/frontmywish/address_default.png");
        background-size: cover;
        margin-right: 20px;
        margin-top: 16px;
    }
    .bottom-line
    {
        margin-bottom: 97px;
    }
    .back-button
    {
        width: 35px;
    }
    .back-img
    {
        width: 10px;
        vertical-align: middle;
    }
</style>
<div class="header">
    <a id="back_url" href="<?=$back_url?>"><span class="back-button"><img class="back-img" src="http://image.matewish.cn/app-wishlist/left.png"></span>确认打赏</a>
</div>
<div class="user-info">
    <?=Html::img($wish->pic1,['class'=>'head-left'])?>
    <div class="head-item head-right">
        <div class="right-up">
            <?=Html::img($wish_user->pic,['class'=>'user-pic-user'])?><labe class="user-name"><?=$wish_user->nick_name?></labe><img class="user-pic-sex" src="http://oss.aliyuncs.com/meiyuannet/wish_web/sex<?=($wish_user->sex === '男'?'1':'2')?>.png"/>
            <label class="left-days">
                <?php
                if($is_over)
                {
                    ?>
                    <?=$left_days?>
                <?php
                }
                else {
                    ?>
                    剩余<?= $left_days ?>天
                <?php
                }
                ?>
            </label>
        </div>
        <div class="right-middle">
        <?=$wish->wish_name?>
        </div>
        <div class="right-bottom">
        ￥<?=strval($wish->wish_money).'/'.strval($wish->ready_reward_money + $wish->red_packets_money)?>&nbsp;&nbsp;&nbsp;<?=$wish->reward_num?>人数打赏
        </div>

    </div>

</div>
<form id="form-reward" onsubmit="return false;">
<div class="reward-input">
    <span class="font-margin">打赏金额</span>
    <div class="reward-txt">￥<input type="text" id="reward_money" placeholder="输入金额" name="WxPay[reward_money]"></div>
</div>
<!--打赏方式  1余额支付  4微信支付-->
    <input type="hidden" id="target" name="WxPay[pay_target]" value="reward"/>
    <input type="hidden" id="pay_type" name="WxPay[pay_type]" value="100" />
<div class="payment">
    <label class="font-margin">支付方式</label>
</div>
<div class="payment-else">
    <div class="check-item check-item-checed">&nbsp;</div>
    <span class="font-margin">微信支付</span>
</div>
<!--<div class="payment-else">
    <span class="font-margin">余额支付</span>
</div>-->
<?php
if($back_type == '1')
{
    ?>
    <div class="pay-address">
    <label>邮箱地址</label><input id="back_info" name="WxPay[email]" type="text" />
</div>
<?php
}
else if($back_type == '2')
{
    ?>
    <div class="pay-address">
        <input type="hidden" name="WxPay[address_id]" id="address_id">
        <label>收货地址</label>&nbsp;&nbsp;&nbsp;
            <a id="choose_address" href="#">
                    <img class="back-img" src="http://image.matewish.cn/app-wishlist/right-1.png">
            </a>
    </div>
    <div class="bottom-line"></div>
<?php
}
    ?>
<input type="hidden" id="default_words" name="WxPay[default_words]" value="<?=$rand_words_default?>">
<div class="footer">
    <div class="pay-button">
    <div class="footer-contain">
        <div class="footer-left">
            <input type="text" placeholder="<?=$rand_words_default?>" name="WxPay[remark2]" id="remark2">
        </div>
        <div class="footer-right">
            <button id="sure_reward" >确认打赏</button>
        </div>
    </div>
    </div>
</div>
</form>
<input type="hidden" value="0" id="issubmit">
<?php
$js='

$(document).on("focus","input[type=\"text\"]",function(){
    $(".footer").css({"position":"relative"});
});

$(document).on("blur","input[type=\"text\"]",function(){
        $(".footer").css({"position":"fixed","bottom":"0px"});
});

var bill_no = null; //账单编号
        //调用微信JS api 支付
        function jsApiCall(payParamStr)
        {
            pay_param = $.parseJSON(payParamStr);
            WeixinJSBridge.invoke(
                "getBrandWCPayRequest",
                pay_param,
                function(res){
                    //支付失败处理
                    if(res.err_msg != "get_brand_wcpay_request:ok" )
                    {
                        if(bill_no != null)
                        {
                            //取消支付
                            $.ajax({
                            type: "POST",
                            url: "/mywish/cancelotherpay?token='.$token.'",
                            data: "bill_no="+bill_no,
                            success: function(data)
                                {
                                //alert(data);
                                   data = $.parseJSON(data);
                                    if(data.code == "0")
                                    {
                                        location = $("#back_url").attr("href");
                                     }
                                     else
                                     {
                                        bill_no = null;
                                         alert("取消支付异常:" + data.msg);
                                     }
                                },
                            error: function (XMLHttpRequest, textStatus, errorThrown)
                                {
                                    bill_no = null;
                                    alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                                 }
                            });
                        }
                    }
                    else
                    {
                        location = $("#back_url").attr("href");
                    }
                    $("#issubmit").val("0");
                    //alert(res.err_code+res.err_desc+res.err_msg);
                }
            );
        }

        function callpay(payParamStr)
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener("WeixinJSBridgeReady", jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent("WeixinJSBridgeReady", jsApiCall);
                    document.attachEvent("onWeixinJSBridgeReady", jsApiCall);
                }
            }else{
                jsApiCall(payParamStr);
            }
        }

        function startWxPay()
        {
            $.ajax({
            type: "POST",
            url: "/mywish/getotherpayparams?token='.$token.'",
            data: $("#form-reward").serialize(),
            success: function(data)
                {
                //alert(data);
                   data = $.parseJSON(data);
                    if(data.code == "0")
                    {
                        bill_no = data.bill_no;
                         //发起支付
                         callpay(data.msg);
                     }
                     else
                     {
                        $("#issubmit").val("0");
                         $("#sure_reward").removeAttr("disabled");
                         bill_no = null;
                         alert("支付异常:" + data.msg);
                     }
                },
            error: function (XMLHttpRequest, textStatus, errorThrown)
                {
                    $("#issubmit").val("0");
                    bill_no = null;
                    alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                    $("#sure_reward").removeAttr("disabled");
                 }
            });
        }

        function startBalancePay()
        {
            //输入密码，再发起支付
        }

        function check_params()
        {
            pay_money = $("#reward_money").val();
            if(pay_money == "" || isNaN(pay_money))
            {
                artDialog.tips("金额不正确");
                return false;
            }
            return true;
        }

        $("#sure_reward").click(function(){
            if(!check_params())
            {
                return false;
            }
            if($("#issubmit").val() == "1")
            {
                return false;
            }
            $("#issubmit").val("1");
            pay_money = $("#reward_money").val();
            pay_type = $("#pay_type").val();
            if(pay_type == "100")
            {
                startWxPay();
            }
            else if(pay_type == "1")
            {
                startBalancePay();
            }
            else
            {
                alert("支付类型错误");
            }
            return false;
        });

        $(function(){
        width = $(window).width();
        $(".footer-left").css("width",width - 135);
        });
';

if($back_type == '2')
{
    $js .= '
$("#choose_address").click(function(){
art.dialog.open("/mywish/addresslist?token='.$token.'",{
    title:"",
    opacity:1,
    width: "100%",
    height: "100%",
    left: "0%",
    top: "0%",
    fixed: true,
    resize: false,
    drag: false,
    close:function(){
        if(art.dialog.data("address_id") != undefined)
        {
            $("#address_id").val(art.dialog.data("address_id"));
        }
    }
});
return false;
});

function closeModal()
{
    art.dialog.close();
}
';
}

$this->registerJs($js);