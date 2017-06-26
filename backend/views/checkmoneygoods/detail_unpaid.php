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
        <p><?='未打款详情'?></p>
    </div>
    <div class="form-group check-button-list">
        <?= Html::button('打款' , ['class' =>'btn btn-success check-pass','id'=>'btn_pass']).'&nbsp;&nbsp;'.'&nbsp;&nbsp;'.Html::button('拒绝' , ['class' =>'btn btn-success check-refuse','id'=>'btn_refuse']).'&nbsp;&nbsp;'.'&nbsp;&nbsp;'. Html::button('取消' , ['data-dismiss'=>'modal','aria-hidden'=>'true','class' =>'btn btn-success'])
        ?>
    </div>
    <div class="user-info">
        <p>拒绝理由</p>
    </div>
 <div class="relate-contain">
        <input type="hidden" id="has_submit" value="0">
        <textarea id="refused_reason" class="refused-reason textarea"><?= $model['refuesd_reason'] ?></textarea>
    </div>
    <div class="user-info">
        <p>相关信息</p>
    </div>
    <div class="relate-contain">
        <input type="hidden" name="check_id" id="check_id" value="<?= $model['record_id'] ?>">
        <ul class="user-item"><li class="user-item-detail cash_type" data-type="<?= $model['cash_type'] ?>">提现方式：<?= $model['cash_type'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">提现票数：<?= $model['ticket_num'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">提现费率：<?= $model['cash_rate'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">除手续费后的提现金额：<?= $model['real_cash_money'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">打款备注：<?= $model['finance_remark'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">创建时间：<?= $model['create_time'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">审核时间：<?= $model['check_time'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">打款时间：<?= $model['finace_ok_time'] ?></li><li class="user-item-detail"></ul>
    </div>
    <div class="user-info">
        <p>用户信息</p>
    </div>
    <div class="user-contain">
        <ul class="user-item">
            <li class="user-item-detail">
                用户名：<?= $model['nick_name'] ?>
            </li>
            <li class="user-item-detail">
                手机号：<?= $model['phone_no'] ?>
            </li>
            <?php if($alipay_or_wechat == 'alipay'){ ?>
                <li class="user-item-detail">
                    真实姓名：<?= $user_info['real_name'] ?>
                </li>
                <li class="user-item-detail">
                    支付宝账号：<?= $user_info['alipay_no'] ?>
                </li>
                <li class="user-item-detail">
                    身份证号：<?= $user_info['identity_no'] ?>
                </li>
            <?php } ?>
        </ul>
    </div>

<?php
$js = '
$(".check-pass").on("click",function(){
    //打款
    if(confirm("确定打款吗？"))
    {
        if($("#has_submit").val() == "1")
        {
            return;
        }
        $("#has_submit").val("1");
        $url = "/checkmoneygoods/pay_batch_money";
        dataStr = "check_res=3&data="+$("#check_id").val()+"&refused_reason=";
        SubmitCheck($url,dataStr);
    }
});

$(".check-refuse").on("click",function(){
        //拒绝打款
        if($("#has_submit").val() == "1")
        {
            return;
        }
        if(confirm("确定拒绝吗？"))
        {
            reason = $("#refused_reason").val();
            reason = reason.trim();
            if(reason == "")
            {
                alert("拒绝理由不能为空");
                return;
            }
            $("#has_submit").val("1");
            $url = "/checkmoneygoods/checkrefuse";
            dataStr = "check_res=6&check_id="+$("#check_id").val()+"&refused_reason="+reason;
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
                    alert("数据提交成功");
                     $("#contact-modal").modal("hide");
                     //location.href="/auditmanage/index";
                     //$("#gd_check_list").yiiGridView("applyFilter");
                     window.location.reload()
                 }
                 else
                 {
                     alert("打款失败：" + data.msg);
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