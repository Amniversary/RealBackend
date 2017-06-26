<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18
 * Time: 16:32
 */

use yii\bootstrap\Html;
?>
    <style>
        .check-title
        {
            font-family: "微软雅黑", arial, sans-serif;
            font-size: 24pt;
            height: 80px;
            line-height:80px;
            text-align: center;
        }
        .check-button-list
        {
            text-align: left;
            margin: 0px 30px;
        }
        p{
            margin: 0px;
        }
        .user-info
        {
            font-family: "微软雅黑", arial, sans-serif;
            font-size: 22px;
            text-align: left;
            height: 50px;
            padding-left: 10px;
            line-height: 50px;
            background-color: #acacac;
            margin: 10px 0px;
        }
        .user-contain
        {
            margin: 0px;
        }
        .user-item
        {
            list-style:none;
            margin: 0px;
            padding: 0px;
            text-align: left;
        }
        .user-item-detail
        {
            display: inline-block;
            font-size: 12pt;
            margin: 5px 10px;
            width: 50%;
        }
        .check-refuse-contain
        {
            margin: 0px;
        }
        .relate-contain
        {
            margin: 0px;
            text-align: left;
        }
        .refused-reason
        {
            display: block;
            width: 30%;
            height: 68px;
            padding: 6px 12px;
            font-size: 14px;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            margin: 0px 20px;
        }
        .textarea
        {
            width: 94%;
        }
    </style>
    <div class="check-title">
        <p><?='审核详情'?></p>
    </div>
    <div class="form-group check-button-list">
        <?= Html::button('通过' , ['class' =>'btn btn-success check-pass','id'=>'btn_pass']).'&nbsp;&nbsp;'. Html::button('拒绝' , ['class' =>'btn btn-success check-refuse','id'=>'btn_refuse']).'&nbsp;&nbsp;'.Html::button('取消' , ['data-dismiss'=>'modal','aria-hidden'=>'true','class' =>'btn btn-success'])
        ?>
    </div>
    <div class="user-info">
        <p>相关信息</p>
    </div>
    <div class="relate-contain">
        <input type="hidden" name="check_id" id="check_id" value="<?= $model['record_id'] ?>">
        <ul class="user-item"><li class="user-item-detail">用户的ID：<span id="uid"><?= $model['user_id'] ?></span></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">用户的密播ID：<?= $model['client_no'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">用户的密播昵称：<?= $model['nick_name'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">用户的真实姓名：<?= $model['user_name'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">用户的手机号码：<?= $model['phone'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">用户的支付宝账号：<?= $model['alipay'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">用户的微信名：<?= $model['wx_name'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">用户的微信号：<?= $model['wx_number'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">兑换的商品：<span id="gift-name"><?= $model['gift_name'] ?></span></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">兑换的时间：<?= $model['change_time'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">用户的地址：<?= $model['address'] ?></li><li class="user-item-detail"></ul>
    </div>

<?php
$js = '
$(".check-pass").on("click",function(){
    //通过审核
    if(confirm("确定审核通过吗？"))
    {
        if($("#has_submit").val() == "1")
        {
            return;
        }
        $("#has_submit").val("1");
        $url = "/changerecord/check_delivery";
        dataStr = "change_state=1&record_id="+$("#check_id").val()+"&gift_name="+$("#gift-name").text()+"&user_id="+$("#uid").text();
        SubmitCheck($url,dataStr);
    }
});
$(".check-refuse").on("click",function(){
        //拒绝审核
        if($("#has_submit").val() == "1")
        {
            return;
        }
        if(confirm("确定拒绝吗？"))
        {
            $("#has_submit").val("1");
            $url = "/changerecord/check_delivery";
            dataStr = "change_state=2&record_id="+$("#check_id").val()+"&gift_name="+$("#gift-name").text()+"&user_id="+$("#uid").text();
            SubmitCheck($url,dataStr);
        }
});

function SubmitCheck($url, dataStr)
{
            $.ajax({
        type: "POST",
        url: $url,
        data: dataStr,
        success: function(data)
            {
                $("#has_submit").val("0");
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                    alert("审核：" + data.msg);
                    alert("数据提交成功");
                     $("#contact-modal").modal("hide");
                     window.location.reload()
                 }
                 else
                 {

                     alert("审核失败：" + data.msg);
                     window.location.reload()
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                $("#has_submit").val("0");
                window.location.reload()
             }
        });
}
';
$this->registerJs($js,\yii\web\View::POS_END);
?>