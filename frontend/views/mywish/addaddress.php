<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<style>
    .address_header
    {
        height: 60px;
        background-color: #FF5757;
        margin: 0px;
        text-align: center;
        vertical-align: middle;
        line-height: 60px;
    }
    .add_address_ttile
    {
        font-size: 18px;
        color: #FFFCFC;
        margin: 0px;
    }
    .save
    {
        float: right;
        margin: 18px 15px 10px 0px;
        font-size: 18px;
        color: #FFFCFC;
        background-color: #FF5757;
        border:none;
    }
    .back_address_list
    {
        float:left;
        margin-left: 15px;
        text-decoration:none;
        font-size: 30px;
        font-weight: bold;
        color: #FFFCFC;
    }
    .error-summary
    {
        border-bottom: 1px solid #E9E9E9;
    }
    .error-summary ul
    {
        margin: 0px;
    }
    .form-group
    {
        margin: 0px;
        height: 46px;
        clear: both;
        line-height: 46px;
        vertical-align: middle;
        text-align: left;
        border-bottom: 1px solid #E9E9E9;
        background-color: #FFFFFF;
    }
    .field-ddl_province
    {
        display: inline-block;
    }
    .field-ddl_city
    {
        display: inline-block;
    }
    .field-ddl_area
    {
        display: inline-block;
    }
    .item-input
    {
        border: none;
        font-size: 14px;
        vertical-align: middle;
    }
    .item-choose
    {
        max-width: 90px;
        font-size: 16px;
        border: none;
    }
    .item-icon
    {
        width: 20px;
        height: 20px;
        vertical-align: middle;
        margin-left: 15px;
    }
    .control-label
    {
        font-size: 18px;
    }
    body
    {
        background-color: #F4F4F4;
    }
    .field-useraddress-is_default
    {
        margin-top: 10px;
        border-top: 1px solid #E9E9E9;
        border-bottom: 1px solid #e9e9e9;
    }
</style>

<?php
$form = ActiveForm::begin(['id'=>'submit-form','method'=>'POST','options'=>['class'=>'sform']]);
?>
<div class="address_header">
        <?php
        echo Html::a('<','/mywish/addresslist?token='.$token,['class'=>'back_address_list']).'&nbsp;&nbsp;&nbsp;&nbsp;'.Html::label('收货地址','',['class'=>'add_address_ttile']).'&nbsp;&nbsp;&nbsp;&nbsp;'. Html::submitButton('保存',['class'=>'save']);
        ?>
    </div>
<?php
echo $form->errorSummary($model,['header'=>false,'footer'=>false]);
echo $form->beginField($model,'contract_user');
echo Html::img('http://image.matewish.cn/frontmywish/name.png',['class'=>'item-icon']);
echo $form->field($model,'contract_user',['options'=>['tag'=>false]])->textInput(['class'=>'item-input','placeholder'=>'请输入姓名'])->error(false)->label(false);
echo $form->endField();
echo $form->beginField($model,'contract_call');
echo Html::img('http://image.matewish.cn/frontmywish/phone.png',['class'=>'item-icon']);
echo $form->field($model,'contract_call',['options'=>['tag'=>false]])->textInput(['class'=>'item-input','placeholder'=>'请输入手机号码'])->error(false)->label(false);
echo $form->endField();
//echo $form->field($model,'province')->dropDownList($province_info,['id'=>'ddl_province','class'=>'ddl_province'])->label(false)->error(false);

//echo $form->beginField($model,'province');
echo $form->beginField($model,'grouparea');
echo Html::img('http://image.matewish.cn/frontmywish/location.png',['class'=>'item-icon']);
echo $form->field($model,'province',['template'=>'{input}','options'=>['tag'=>false]])->dropDownList($province_info,['id'=>'ddl_province','class'=>'item-input item-choose','tag'=>false])->label(false);
echo $form->field($model,'city',['options'=>['tag'=>false]])->dropDownList([],['id'=>'ddl_city','class'=>'item-input item-choose'])->label(false)->error(false);
echo $form->field($model,'area',['options'=>['tag'=>false]])->dropDownList([],['id'=>'ddl_area','class'=>'item-input item-choose'])->label(false)->error(false);
echo $form->endField();
echo $form->field($model,'province',['options'=>['tag'=>false]])->hiddenInput(['id'=>'addre_province'])->label(false)->error(false);
echo $form->field($model,'city',['options'=>['tag'=>false]])->hiddenInput(['id'=>'addre_city'])->label(false)->error(false);
echo $form->field($model,'area',['options'=>['tag'=>false]])->hiddenInput(['id'=>'addre_area'])->label(false)->error(false);
//echo $form->endField();

//echo $form->field($model,'city')->dropDownList([],['id'=>'ddl_city','class'=>'ddl_city'])->label(false)->error(false);
//echo $form->field($model,'area')->dropDownList([],['id'=>'ddl_area','class'=>'ddl_area'])->label(false)->error(false);
?>
<?php
echo $form->beginField($model,'address');
echo Html::img('http://image.matewish.cn/frontmywish/address.png',['class'=>'item-icon']);
echo $form->field($model,'address',['options'=>['tag'=>false]])->textInput(['class'=>'item-input','placeholder'=>'街道地址门牌号'])->error(false)->label(false);
echo $form->endField();
echo $form->beginField($model,'is_default');
echo Html::img('http://image.matewish.cn/frontmywish/default.png',['class'=>'item-icon','id'=>'img_choose_default']);
echo $form->field($model,'is_default',['options'=>['tag'=>false]])->hiddenInput(['class'=>'is_default'])->error(false)->label('默认地址',['for'=>'useraddress-is_default']);
echo $form->endField();
?>

<?php
$form->end();
$js = '
$(function(){
    $("#ddl_province").change();
});

$("#ddl_province").on("change",function(){
    $("#addre_province").val($(this).find("option:selected").text());
    $.ajax({
        type: "POST",
        url: "/mywish/getcitylist?token='.$token.'",
        data: "province_id="+$(this).val(),
        success: function(data)
            {
            //alert(data);
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                    $("#ddl_city").html(data.msg);
                    $("#ddl_city").change();
                 }
                 else
                 {
                     alert("获取城市数据失败：" + data.msg);
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
             }
        });
});

$("#ddl_city").on("change",function(){
    $("#addre_city").val($(this).find("option:selected").text());
    $.ajax({
        type: "POST",
        url: "/mywish/getarealist?token='.$token.'",
        data: "city_id="+$(this).val(),
        success: function(data)
            {
            //alert(data);
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                    if(data.msg.length > 0)
                    {
                        $("#ddl_area").show();
                        $("#ddl_area").html(data.msg);
                        $("#ddl_area").change();
                    }
                    else
                    {
                        $("#ddl_area").hide();
                    }
                 }
                 else
                 {
                     alert("获取县区数据失败：" + data.msg);
                 }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
             }
        });
});

$("#ddl_area").on("change",function(){
    $("#addre_area").val($(this).find("option:selected").text());
});

$("#img_choose_default").click(function(){
    is_default = $("#useraddress-is_default").val();
    if(is_default == "1")
    {
        $(this).attr("src","http://image.matewish.cn/frontmywish/default.png");
        $("#useraddress-is_default").val("0");
    }
    else
    {
        $(this).attr("src","http://image.matewish.cn/frontmywish/default_choose.png");
        $("#useraddress-is_default").val("1");
    }
});

';
$this->registerJs($js);
?>