<?php
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
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
        form
        {
            width: 200px;
            margin-left: 10px;
        }
    </style>
    <div class="check-title">
        <p><?='设置账户【'.strval($user->username).'】的审核号'?></p>
    </div>
    <div class="form-group check-button-list">
        <?= Html::button('设置审核号' , ['class' =>'btn btn-success check-refuse','id'=>'btn_refuse']).'&nbsp;&nbsp;'.Html::button('返回' , ['data-dismiss'=>'modal','aria-hidden'=>'true','class' =>'btn btn-success'])

        ?>
    </div>
    <div class="user-info">
        <p>审核号信息(审核号必须在0-19内)</p>
    </div>
    <div class="relate-contain">
        <?php $form = ActiveForm::begin(['id'=>'set_check_no']); ?>
        <?= $form->field($model, 'start_no')->textInput() ?>
        <?= $form->field($model, 'end_no')->textInput() ?>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="bottom-div"></div>
<?php
$js = '
$(".check-refuse").on("click",function(){
        if($("#has_submit").val() == "1")
        {
            return;
        }
        //拒绝审核
        if(confirm("确定设置审核号吗？"))
        {
            $("#has_submit").val("1");
            $url = "/usermanage/setcheckno?user_id='.$user->backend_user_id.'";
            SubmitCheck($url,$("#set_check_no").serialize());
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
                     $("#user-manage-list").yiiGridView("applyFilter");
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