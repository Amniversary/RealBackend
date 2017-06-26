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
        <p><?='已审核详情'?></p>
    </div>
    <div class="user-info">
        <p>审核备注</p>
    </div>
    <div class="relate-contain">
        <input type="hidden" id="has_submit" value="0">
        <p><?= $model['remark3'] ?></p>
    </div>
    <div class="user-info">
        <p>相关信息</p>
    </div>
    <div class="relate-contain">
        <input type="hidden" name="check_id" id="check_id" value="<?= $model['report_id'] ?>">
        <ul class="user-item"><li class="user-item-detail">状态：<?= $model['status'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">举报人昵称：<?= $model['nick_name'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">场景：<?= $model['scene'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">举报类型：<?= $model['report_type'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">被举报人昵称：<?= $model['report_user_name'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">举报内容：<?= $model['report_content'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">举报人电话：<?= $model['remark2'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">被举报人电话：<?= $model['remark1'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">创建时间：<?= $model['create_time'] ?></li><li class="user-item-detail"></ul>
        <ul class="user-item"><li class="user-item-detail">审核人：<?= $model['remark4'] ?></li><li class="user-item-detail"></ul>

    </div>
<?php
$js = '
$(".check-refuse").on("click",function(){
        //拒绝审核
        if($("#has_submit").val() == "1")
        {
            return;
        }
        if(confirm("确定审核通过吗？"))
        {
            reason = $("#refused_reason").val();
            reason = reason.trim();
            if(reason == "")
            {
                alert("审核备注不能为空");
                return;
            }
            $("#has_submit").val("1");
            $url = "/checkreport/checkrefuse";
            dataStr = "check_id="+$("#check_id").val()+"&remark1="+reason;
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