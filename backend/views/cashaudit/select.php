<?php
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
        margin: 0px 10px;
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
    <p><?='提现账单详情'?></p>
</div>
<div class="form-group check-button-list">
    <?php if(in_array($model['status'], [1, 0])) {
        echo Html::button('打款' , ['class' =>'btn btn-success check-pass','id'=>'btn_pass']).'&nbsp;&nbsp;&nbsp;&nbsp;';
    }
    ?>
    <?= Html::button('取消' , ['data-dismiss'=>'modal','aria-hidden'=>'true','class' =>'btn btn-success']) ?>
</div>
<div class="user-info">
    <p>账单相关信息</p>
</div>
<div class="relate-contain">
    <input type="hidden" id="has_submit" value="0">
</div>
<div class="relate-contain">
    <ul class="user-item"><li class="user-item-detail">提现金额：<?= $model['money'] ?>元</li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">打款金额：<?= $model['result_money'] ?>元</li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">提现费率：<?= $model['cash_rate'] ?>%</li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">商户订单号：<?= $model['partner_trade_no'] ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">微信账单号：<?= $model['payment_no'] ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">操作唯一码：<?= $model['op_unique_no'] ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">支付成功时间：<?= $model['payment_time'] ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">创建时间：<?= date('Y-m-d H:i:s', $model['create_at']) ?></li><li class="user-item-detail"></ul>
    <ul class="user-item"><li class="user-item-detail">支付失败原因：<?= $model['err_reason'] ?></li><li class="user-item-detail"></ul>
</div>
<div class="user-info">
    <p>用户信息</p>
</div>
<div class="user-contain">
    <ul class="user-item">
        <ul class="user-item"><li class="user-item-detail">用户名：<?= $model['nick_name'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">真实姓名：<?= $model['name'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">OpenId：<?= $model['open_id'] ?></li><li class="user-item-detail"></ul>
    </ul>
</div>

<?php
$js = '
$(".check-pass").on("click",function(){
    if(confirm("确定提交打款任务吗？")) {
        if($("#has_submit").val() == "1") {
            return;
        }
        $("#has_submit").val("1");
        $url = "/cashaudit/update";
        dataStr = "id='.$model['id'].'";
        SubmitCheck($url,dataStr);
    }
});

function SubmitCheck($url,dataStr)
{
    $.ajax({
        type:"POST",
        url:$url,
        data:dataStr,
        success:function(data) {
            data = $.parseJSON(data);
            $("#has_submit").val("0");
            if(data.code == 0) {
                alert("打款任务提交成功");
                $("#contact-modal").modal("hide");
                $("#cash_list").yiiGridView("applyFilter");
            } else {
                alert("提交失败：" + data.msg);
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            $("#has_submit").val("0");
            alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
        }
    });
}

$(function(){
    $("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
});
';
$this->registerJs($js,\yii\web\View::POS_END);