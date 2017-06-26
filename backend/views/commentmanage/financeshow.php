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
            width: 90%;
            text-align: left;
            margin: 0px auto;
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
        .relate-contain
        {
            margin: 0px 0px 30px 0px;
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
            margin: 0px auto;
        }
        .bottom-div
        {
            height: 1px;
        }
    </style>
    <div class="check-title">
        <p><?='将id为【'.strval($comment->wish_comment_id).'】的评论禁止'?></p>
    </div>
    <div class="form-group check-button-list">
        <?= Html::button('禁止' , ['class' =>'btn btn-success check-refuse','id'=>'btn_refuse']).'&nbsp;&nbsp;'.Html::button('返回' , ['data-dismiss'=>'modal','aria-hidden'=>'true','class' =>'btn btn-success'])

        ?>
    </div>
    <div class="user-info">
        <p>备注</p>
    </div>
    <div class="relate-contain">
        <input type="hidden" id="has_submit" value="0">
        <textarea id="refused_reason" class="refused-reason"></textarea>
    </div>
    <div class="bottom-div"></div>
<?php
$js = '
$(".check-refuse").on("click",function(){
        //拒绝审核
        if(confirm("确定禁止吗？"))
        {
            if($("#has_submit").val() == "1")
            {
                return;
            }
            reason = $("#refused_reason").val();
            reason = reason.trim();
            if(reason == "")
            {
                alert("禁用备注不能为空");
                return;
            }
            $("#has_submit").val("1");
            $url = "/commentmanage/forbid?comment_id='.$comment->wish_comment_id.'";
            dataStr = "remark="+reason;
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
                     $("#wish_comment_list").yiiGridView("applyFilter");
                 }
                 else
                 {
                     alert("设置失败：" + data.msg);
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                $("#has_submit").val("0");
             }
        });
}
$(function(){
$("#contact-modal").on("hide.bs.modal",function(e){$(this).removeData();});
});
';
$this->registerJs($js,\yii\web\View::POS_END);
?>