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
        .check-list
        {
            line-height: 20px;
            margin-left: 40px;
            display: inline-block;
            vertical-align: top;
        }
        .lebal-dis
        {
            display: block;
        }
        .modal-lg{
            width:90%;
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
            margin: 0px 0px 30px 5px;
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
            margin-left: 10px;
        }
        .btn_check_all
        {
            width: 25px;
            height: 25px;
            background-color: #fff;
            color: #008d4c;
            border: 1px solid;
            border-radius: 3px;
            vertical-align: middle;
            text-align: center;
            /*font-weight: bold;*/
        }
    </style>
    <div class="check-title">
        <p><?='选择标签'?></p>
    </div>
    <div class="form-group check-button-list">
        <?php
        echo Html::button('选择标签' , ['class' =>'btn btn-primary check-refuse','id'=>'btn_refuse']).'&nbsp;&nbsp;'.
        Html::button('返回' , ['data-dismiss'=>'modal','aria-hidden'=>'true','class' =>'btn btn-primary']);
        ?>
    </div>
    <div class="user-info">
        <p>标签信息</p>
    </div>
    <div class="relate-contain">
        <?php $form = ActiveForm::begin(['id'=>'set_title']);

        foreach($rights as $right) {
            $options = ['class'=>'check-list','itemOptions'=>['labelOptions'=>['class'=>'lebal-dis']]];
            echo Html::checkboxList('title',$selections,$right,$options);
        }

        ?>
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
        else
        {
            $("#has_submit").val("1");
            $url = "/publiclist/set_tag_list";
            SubmitCheck($url,$("#set_title").serialize());
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
//                data = $.parseJSON(data);
//                if(data.code == "0")
//                {
//                    alert("数据提交成功");
                    $("#contact-modal").modal("hide");
                    $("#public_list").yiiGridView("applyFilter");
//                }
//                else
//                {
//                     alert("设置失败：" + data.msg);
//                }
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