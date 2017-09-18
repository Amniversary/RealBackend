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
        #drop-auth{
            height:2.4em;
        }
        #drop-1{
            margin: 0 0 30px 5px;
            text-align: left;
        }
        #drop-2{
            margin: 0 0 30px 5px;
            text-align: left;
        }
    </style>
    <div class="check-title">
        <p><?='设置公众号'?></p>
    </div>
    <div class="form-group check-button-list">
        <?php
                echo Html::button('&nbsp;' , ['class'=>'btn_check_all']).'&nbsp;&nbsp;'
                    .Html::button('设置公众号' , ['class' =>'btn btn-success check-refuse','id'=>'btn_refuse']).'&nbsp;&nbsp;';
        echo Html::button('返回' , ['data-dismiss'=>'modal','aria-hidden'=>'true','class' =>'btn btn-success']).'&nbsp;&nbsp;';
        echo Html::dropDownList('list',['1'],['1'=>'全部','2'=>'已认证','3'=>'未认证'],['class'=>'btn btn-success', 'id'=> 'drop-auth']);
        ?>
    </div>
    <div class="user-info">
        <p>公众号信息</p>
    </div>
    <div class="relate-contain" id="drop-0">
        <?php $form = ActiveForm::begin(['id'=>'set_title1']);

            foreach($rights as $right)
            {
                $options = ['class'=>'check-list','itemOptions'=>['labelOptions'=>['class'=>'lebal-dis']]];
                echo Html::checkboxList('title',$selections,$right,$options);
            }

        ?>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="drop-1">
        <?php $form = ActiveForm::begin(['id'=>'set_title2']);

        foreach($params_one as $right)
        {
            $options = ['class'=>'check-list','itemOptions'=>['labelOptions'=>['class'=>'lebal-dis']]];
            echo Html::checkboxList('title',$selections,$right,$options);
        }

        ?>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="drop-2">
        <?php $form = ActiveForm::begin(['id'=>'set_title3']);

        foreach($params_two as $right)
        {
            $options = ['class'=>'check-list','itemOptions'=>['labelOptions'=>['class'=>'lebal-dis']]];
            echo Html::checkboxList('title',$selections,$right,$options);
        }
        ?>
        <?php ActiveForm::end(); ?>

    </div>
    <div class="bottom-div"></div>
<?php
$js = '
$(".btn_check_all").click(function() {
        if ($(".btn_check_all").val() == "全选") {
            $("input[type=\'checkbox\']:checkbox").each(function() { $(this).prop("checked", false); });
            $(".btn_check_all").val("取消");
            $(".btn_check_all").html("&nbsp;");
        }
        else {
            $("input[type=\'checkbox\']:checkbox").each(function() { $(this).prop("checked", true); });
            $(".btn_check_all").val("全选");
            $(".btn_check_all").html("✓");
        }
    });

$(document).ready(function(){
    $("#drop-1, #drop-2").hide();
});
$("#drop-auth").change(function(){
    $vue = $("#drop-auth option:selected").val();
    if($vue == "1") {
        $("#drop-0").show();
        $("#drop-1,#drop-2").hide();
    }else if($vue == "2") {
        $("#drop-0, #drop-2").hide();
        $("#drop-1").show();
    }else if($vue == "3") {
        $("#drop-2").show();
        $("#drop-0, #drop-1").hide();
    }
});

$(".check-refuse" ).on("click",function(){
        if($("#has_submit").val() == "1")
        {
            return;
        }
        //拒绝审核
//        $hide =  $("input[name=\'title[]\']:checked").length;
//        if($hide <= 0)
//        {
//            alert("至少勾选一项权限!");
//        }
        else
        {
            if(confirm("确定设置公众号吗？"))
            {
                $("#has_submit").val("1");
                $url = "/batchkeyword/setauthlist?key_id='.$keyword->key_id.'";
               $vue = $("#drop-auth option:selected").val();
                SubmitCheck($url,$("#set_title" + $vue).serialize());
            }
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

/*if (!$haveSetPower) {
    $js .= '$("#set_title").find("[type=checkbox]").attr("disabled", true);';
}*/
$this->registerJs($js,\yii\web\View::POS_END);
?>