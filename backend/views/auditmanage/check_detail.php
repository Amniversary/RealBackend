<?php
use yii\bootstrap\Html;
?>
<style>
    .check-title
    {
        font-family: "微软雅黑", arial, sans-serif;
        font-size: 26pt;
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
        width: 30%;
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
        width: 90%;
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
</style>
<div class="check-title">
    <p><?='【'.$check_record->GetCheckTypeName().'】审核'?></p>
</div>
<div class="form-group check-button-list">
        <?= Html::button('通过' , ['class' =>'btn btn-success check-pass','id'=>'btn_pass']).'&nbsp;&nbsp;'. Html::button('拒绝' , ['class' =>'btn btn-success check-refuse','id'=>'btn_refuse']).'&nbsp;&nbsp;'.Html::button('取消' , ['data-dismiss'=>'modal','aria-hidden'=>'true','class' =>'btn btn-success'])

        ?>
</div>
<div class="user-info">
    <p>拒绝理由</p>
</div>
<div class="relate-contain">
    <input type="hidden" id="has_submit" value="0">
    <input type="hidden" name="check_id" id="check_id" value="<?=$check_record->business_check_id?>">
<textarea id="refused_reason" class="refused-reason"></textarea>
</div>
<div class="user-info">
    <p>相关信息</p>
</div>
<div class="relate-contain">
<?php
echo $this->render($other_view,['data'=>$relate_data]);
?>
</div>
<div class="user-info">
    <p>用户信息</p>
</div>
<div class="user-contain">
    <ul class="user-item"><li class="user-item-detail">姓名：<?=$base_centification['user_name']?></li><li class="user-item-detail">身份证号：<?=$base_centification['identity_no']?></li><li class="user-item-detail">用户类型：<?=$user->GetUserTypeName()?></li></ul>
    <ul class="user-item"><li class="user-item-detail">认证级别：<?=$user->GetLevelName()?></li><li class="user-item-detail">借款次数：<?=$user_active->fund_cash_count?>次</li><li class="user-item-detail">借款总金额：<?=$user_active->fund_cash_money?>￥</li></ul>
    <ul class="user-item"><li class="user-item-detail">还款次数：<?=$user_active->fund_back_count?>次</li><li class="user-item-detail">还款金额：<?=$user_active->fund_back_money?>￥</li><li class="user-item-detail">逾期还款：<?=$user_active->delay_times?>次</li></ul>
</div>
<div class="user-info">
    <p>审核拒绝信息</p>
</div>
<div class="check-refuse-contain">
    <?= \yii\grid\GridView::widget([
        'dataProvider'=>$dataProvider,
        'columns'=>$data_columns,
        'layout'=>'{items}'
    ]);
    ?>
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
        $url = "/auditmanage/checkrst";
        dataStr = "check_rst=1&check_id="+$("#check_id").val()+"&refused_reason=";
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
            reason = $("#refused_reason").val();
            reason = reason.trim();
            if(reason == "")
            {
                alert("拒绝理由不能为空");
                return;
            }
            $("#has_submit").val("1");
            $url = "/auditmanage/checkrst";
            dataStr = "check_rst=0&check_id="+$("#check_id").val()+"&refused_reason="+reason;
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
                     $("#gd_check_list").yiiGridView("applyFilter");
                 }
                 else
                 {
                     alert("审核失败：" + data.msg);
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                $("#has_submit").val("0");
             }
        });
}
';
$this->registerJs($js,\yii\web\View::POS_END);
?>