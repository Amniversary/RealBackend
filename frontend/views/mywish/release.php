<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/5
 * Time: 9:29
 */
  \common\assets\ArtDialogAsset::register($this);
  \common\assets\WxAsset::register($this);
    \common\assets\QueryMd5Asset::register($this);
?>
<style xmlns="http://www.w3.org/1999/html">
    *{ margin: 0; padding: 0;}
    *, *:after, *:before {
        box-sizing: border-box;
    }
    body{
        font-family: "微软雅黑", arial, sans-serif;
        background-color: #f4f4f4;
        font-size: 16px;
    }
    .header{
        height: 50px;
        background-color: #ff5757;
        text-align: center;
        line-height: 50px;
        font-size: 16px;
        border-bottom: 1px solid #E5E5E5;
    }
    .header a{
        text-decoration: none;
        color: #fff;
        margin-right: 5px;
    }
    .form-text{ margin-left: 15px; vertical-align: middle;}
    .form-wish-list{
        height: 45px;
        background-color: #fff;
        border-bottom: 1px solid #ebebeb;
        line-height: 43px;
    }
    .form-wish-time{
        height: 45px;
        background-color: #fff;
        border-bottom: 1px solid #ebebeb;
        border-top: 1px solid #ebebeb;
        line-height: 40px;
        margin-top: 10px;
    }
    .form-wish-back{
        height: 45px;
        background-color: #fff;
        border-top: 1px solid #ebebeb;
        line-height: 40px;
    }
    .form-wish-back img{
        width: 30px;
        float: right;
        margin-right: 15px;
        margin-top: 7px;
    }
    .form-btn{
        position: fixed;
        background-color: #fff;
        bottom: 0;
        width: 100%;
    }
    .form-btn button{
        color: #fff;
        background-color: #ff5757;
        border: none;
        width: 100%;
        height: 45px;
        font-size: 16px;
        font-family: "微软雅黑", arial, sans-serif;
    }
    .form-txt-img{
        background-color: #fff;
        border-bottom: 1px solid #ebebeb;
    }
    .form-deadline{
        height: 25px;
        line-height: 25px;
    }
    .form-time-end{
        float: right;
        color: #666678;
        font-size: 14px;
        margin-right: 15px;
    }
    .form-wish-list input{
        display: inline-block;
        border: none;
        padding-top: 3px;
        outline: none;
        -webkit-tap-highlight-color: rgba(255,0,0,0);
        font-size: 16px;
        vertical-align: middle;
    }
    .form-txt{
        width: 100%;
        height: 100px;
        border: none;
        outline: none;
        resize: none;
        margin-top: 10px;
        font-size: 14px;
        -webkit-tap-highlight-color:rgba(255,0,0,0);
    }
    .form-txt-lead{
        overflow: hidden;
        margin: 0 15px;
    }
    .form-img-lead{
        display: inline-block;
        margin-left: 15px;
        vertical-align: middle;
        overflow: hidden;
    }
    .release-img
    {
        height: 60px;
        width: 60px;
        border: 1px solid #bbb;
        font-size: 26px;
        cursor: pointer;
        -webkit-tap-highlight-color:rgba(255,0,0,0);
    }
    .release-hidden{
        margin-bottom: 150px;
    }
    .date-number{
        float: right;
        width: 200px;
        height: 42px;
        text-align: center;

    }
    .date-number button{
        width: 22px;
        height: 22px;
        background-color: #ff5757;
        font-size: 16px;
        color: #fff;
        border: none;
        vertical-align: middle;
        -webkit-tap-highlight-color:rgba(255,0,0,0);
    }
    .date-number input{
        width: 50px;
        height: 20px;
        text-align: right;
        border: none;
        outline: none;
        font-size: 14px;
        -webkit-tap-highlight-color:rgba(255,0,0,0);
    }
    .date-number label{
        color: #666678;
        font-size: 14px;
        margin-right: 5px;
    }
    .date-number span{
        border: 1px solid #666666;
        vertical-align: middle;
    }
    .wish-time{
        display: inline-block;
    }
    .form-wish-retuen{
        background-color: #fff;
        border-bottom: 1px solid #ebebeb;
    }
    .release-wish-back {
        height: 45px;
        border-bottom: 1px solid #ebebeb;
        border-top: 1px solid #ebebeb;
        line-height: 40px;
    }
    .release-wish-back img{
        width: 30px;
        float: right;
        margin-right: 15px;
        margin-top: 5px;
    }
    .release-example{
        margin-left: 15px;
        vertical-align: middle;
        margin-bottom: 25px;
    }
    .img-item
    {
        width: 60px;
        height: 60px;
    }
    .img-item-contain
    {
        margin-bottom: 5px;
    }
    .item-pic-delete
    {
        width: 15px;
        height: 15px;
        position: absolute;
        top: 1px;
        left: 1px;
    }
    .item-delete-contain
    {
        position: absolute;
    }
    section
    {
        float: right;
        min-width: 85px;
        width: 3%;
        padding: 3px 0;
        min-height: 40px;
    }
    .checkbox
    {
        position: relative;
        display: inline-block;

    }
    .model-1
    {
        line-height: normal;
    }
    .checkbox input {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        z-index: 100;
        opacity: 0;
        display: block;
        -webkit-appearance: none;
        -webkit-tap-highlight-color:rgba(255,0,0,0);

    }
    .model-1 .checkbox label {
        background: #fff;
        border: 1px solid #eee;
        height: 33px;
    }
    .checkbox label
    {
        width: 71px;
        height: 42px;
        background: #ccc;
        position: relative;
        display: inline-block;
        border-radius: 46px;
        -webkit-transition: 0.4s;
        transition: 0.4s;
    }
    .model-1 .checkbox label:after {
        background: #bbb;
        top: 3px;
        left: 5px;
        width: 25px;
        height: 25px;
    }
    .checkbox label:after {
        content: '';
        position: absolute;
        width: 30px;
        height: 30px;
        border-radius: 100%;
        left: 0;
        top: -1px;
        z-index: 2;
        background: #fff;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        -webkit-transition: 0.4s;
        transition: 0.4s;
    }
    .model-1 .checkbox input:checked + label:after {
        background: #3eb454;
    }
    .checkbox input:checked + label:after {
        left: 40px;
    }
    .form-radio
    {
        float: right;
        -webkit-appearance: none;-webkit-appearance: none;
        height: 30px;
        width: 30px;
        border: 1px solid #ebebeb;
        border-radius: 50%;
        margin-top: 7px;
        margin-right: 15px;
        -webkit-tap-highlight-color:rgba(255,0,0,0);
    }
    .form-radio:checked
    {
        display: block;
        border: none;
        width: 30px;
        height: 30px;
        content:url("http://image.matewish.cn/frontmywish/rights-img.png");
    }
    .form-txt-area1::-webkit-input-placeholder::after
    {
        display: block;
        content: "打赏0-2元，可以邮寄一张明信片；\A 打赏2.1-10元，可以邮寄一本纪念册；\A 打赏10.1元以上， 可以邮寄一条防晒围巾。";
    }
    .form-txt-area1
    {
        width: 100%;
        height: 70px;
        border: none;
        outline: none;
        resize: none;
        margin-top: 10px;
        font-size: 14px;
        -webkit-tap-highlight-color:rgba(255,0,0,0);
    }
    .form-txt-area2
    {
        width: 100%;
        height: 40px;
        border: none;
        outline: none;
        resize:none;
        margin-top: 10px;
        font-size: 14px;
        -webkit-tap-highlight-color: rgba(255,0,0,0);
    }
    .upload-file
    {
        position: relative;
        width: 0px;
        height: 60px;
        display: block;
        -webkit-appearance: none;
        outline: none;
        border: none;
        -webkit-tap-highlight-color:rgba(255,0,0,0);
    }
.upload-file:before
{
    content: "+";
    width: 60px;
    height: 60px;
    border: 1px solid #bbb;
    font-size: 38px;
    color: #E5E5E5;
    cursor: pointer;
    -webkit-tap-highlight-color:rgba(255,0,0,0);
    float: left;
    text-align: center;
    line-height: 60px;
    z-index: 100;
    opacity: 1;
}

.filewidth
{
    width:60px;
}

</style>
<div class="header">
    <a href="#">发布愿望</a>
</div>
<form id="form-release" onsubmit="return false;">
    <div class="form-wish-list">
        <span class="form-text">愿望名称：</span>
        <input type="text" id="wish_name" name="Wish[wish_name]" maxlength="16"  placeholder="16个字符以内" >
    </div>
    <div class="form-wish-list">
        <span class="form-text">期望金额（元）：</span>
        <input type="tel" id="wish_money" name="Wish[wish_money]" maxlength="10" placeholder="输入整数" style="width: 130px" >
    </div>
    <div class="form-txt-img">
        <div class="form-txt-lead">
            <textarea class="form-txt" id="description" name="Wish[discribtion]" placeholder="只要坚持,愿望总是可以实现的。请详细描述您的愿望,美愿帮您一起来实现。"></textarea>
        </div>
        <input type="hidden" id="pic1" name="Wish[pic1]">
        <input type="hidden" id="pic2" name="Wish[pic2]">
        <input type="hidden" id="pic3" name="Wish[pic3]">
        <input type="hidden" id="pic4" name="Wish[pic4]">
        <input type="hidden" id="pic5" name="Wish[pic5]">
        <input type="hidden" id="pic5" name="Wish[pic6]">
        <div class="img-item-contain" id="img-item-contain">
        <div class="form-img-lead filewidth" id="upload_button_content">
            <input type="file" class="upload-file" id="upload_file" />
<!--            <button id="btn-pic-add" class="release-img">+</button>-->
        </div>
        </div>
    </div>
    <div class="form-wish-time">
        <span class="form-text">愿望期限</span>
        <div class="date-number">
            <button id="btn-down" >-</button>
            <div class="wish-time"><input id="time" type="text" maxlength="3" placeholder="1~999" ><label>&nbsp;天</label></div>
            <button id="btn-up" >+</button>
        </div>
    </div>
    <div class="form-deadline">
        <label class="form-time-end" id="time-end"></label><!--*=>日期-->
        <input type="hidden" name="Wish[end_date]" id="endtime" >
    </div>
    <div class="form-wish-back">
        <span class="form-text">我的回报</span>
        <section class="model-1">
            <div class="checkbox">
                <input type="checkbox" id="back_check" />
                <label></label>
                <input type="hidden" name="Wish[back_type]" id="back_type">
            </div>
        </section>
    </div>
    <div class="form-wish-retuen">
        <div class="release-wish-back">
            <span class="form-text">实物回报<small style="color: #666666">（需邮寄）</small></span>
            <input type="radio" class="form-radio" name="form-radio" value="2" checked>
        </div>
        <div class="release-example">
            <textarea class="form-txt-area1" id="back_dis" name="Wish[back_dis]" placeholder="例如：" rows="5"></textarea>
        </div>
        <div class="release-wish-back">
            <span class="form-text">虚拟回报<small style="color: #666666">（无需邮寄）</small> </span>
            <input type="radio" class="form-radio" name="form-radio" value="1" >
        </div>
        <div class="release-example">
            <textarea class="form-txt-area2" id="back_dis1" name="Wish[back_dis1]" placeholder="例如：可以邮箱发送电子邮件等。" rows="5"></textarea>
        </div>
    </div>
    <div class="release-hidden"></div>
    <div class="form-btn">
        <button id="btn-sub">马上许愿</button>
    </div>
    <input type="hidden" id="issubmit" value="0"/>
    <input type="hidden" id="longitude" name="Wish[longitude]">
    <input type="hidden" id="latitude" name="Wish[latitude]">
</form>
<script type="text/html" id="pic-item-template">
    <div class="form-img-lead">
        <div class="item-delete-contain">
            <img class="item-pic-delete" data-id="{id}" src="http://image.matewish.cn/frontwebmatewish/wishdelete.png" />
        </div>
        <img class="img-item" src="{src}" />
    </div>
</script>
<?php
$js = '
artDialog.tips = function (content, time) {
    return artDialog({
        id: "Tips",
        title: false,
        cancel: false,
        fixed: true,
        lock: true
    })
    .content("<div style=\"padding: 0 1em;\">" + content + "</div>")
    .time(time || 1);
};
var picItem = [6,5,4,3,2,1];
var okPicItem=[];
var okPicFiles=[];
var okPicFilesIndex=[];
var curPicFile=null;
var curItem = 0;
var dialog = null;
$("#btn-pic-add").click(function(){
    if(picItem.length <= 0 && curItem == 0)
    {
        artDialog.tips("最多添加5张图片");
        return false;
    }

    if(curItem == 0)
    {
        curItem = picItem.pop();
    }
    dialog = art.dialog({
        title:"请选择上传图片",
        fixed:true,
        lock:true,
        content:[
            "<form id=\"form-upload\"> </form><input type=\"file\" class=\"upload-file\" id=\"upload_file"+curItem+"\"/></form>"
        ].join(""),
        ok:false
    });
    return false;
});

$("document").ready(function(){
    $(".form-wish-retuen").hide();
    $("#back_type").val(4);


});

$("#back_check").click(function()
{
    if($("#back_check").prop("checked"))
    {
        $("#back_type").val(2);
        $(".form-wish-retuen").slideToggle();
    }
    else
    {
        $("#back_type").val(4);
        $(".form-wish-retuen").slideToggle();
    }
});
$("#wish_money").keypress(function()
{
    var keyCode = event.which;
        if(keyCode >= 48 && keyCode <=57)
            return true;
        else
            return false;
        }).focus(function() {
            this.style.imeMode="disabled";
});
$("#time").keypress(function(event){
    var keyCode = event.which;
        if (keyCode >= 48 && keyCode <=57)
            return true;
        else
            return false;
        }).focus(function() {
            this.style.imeMode="disabled";
});

$("#time").keyup(function()
{
    var textnum = $("#time").val();
    var time =new Date();
    if(textnum == "")
    {
        textnum = 0;
    }
    time.setDate(time.getDate() + parseInt(textnum));
        var showtime = time.getFullYear() + "年" + (time.getMonth()+1) + "月" + time.getDate() + "日截止";
        $("#time-end").html(showtime);
        var endtime = time.getFullYear() + "-" + (time.getMonth()+1) + "-" + time.getDate();
        $("#endtime").val(endtime);
});

$("#btn-up").click(function()
{
    var num = $("#time").val();
        if(num >= 0)
        {
            num++;
        $("#time").val(num);
        var time =new Date();
        var textnum = $("#time").val();
        time.setDate(time.getDate() + parseInt(textnum));
        var showtime = time.getFullYear() + "年" + (time.getMonth()+1) + "月" + time.getDate() + "日截止";
        $(".form-time-end").html(showtime);
        var endtime = time.getFullYear() + "-" + (time.getMonth()+1) + "-" + time.getDate();
        $("#endtime").val(endtime);
}});

$("#btn-down").click(function()
{
    var num = $("#time").val();
        if(num > 0)
        {
            num--;
        $("#time").val(num);
        var time =new Date();
        var textnum = $("#time").val();
        time.setDate(time.getDate() + parseInt(textnum--));
        var showtime = time.getFullYear() + "年" + (time.getMonth()+1) + "月" + time.getDate() + "日截止";
        $(".form-time-end").html(showtime);
        var endtime = time.getFullYear() + "-" + (time.getMonth()+1) + "-" + time.getDate();
        $("#endtime").val(endtime);
}});

function Judge()
{
    name = $("#wish_name").val();
    money = $("#wish_money").val();
    description = $("#description").val();
    endtime = $("#endtime").val();
    backcheck = $("#back_check").val();
    type = $("#back_type").val();
    time = $("#time").val();
    backdis = $("input[type=\'radio\']:checked").val();
    txtarea = $("#back_dis").val();
    txtarea1 = $("#back_dis1").val();
    longitude = $("#longitude").val();
    latitude = $("#latitude").val();
    pic1 = $("#pic1").val();
    pic2 = $("#pic2").val();
    pic3 = $("#pic3").val();
    pic4 = $("#pic4").val();
    pic5 = $("#pic5").val();
    picitem =new Array(pic1,pic2,pic3,pic4,pic5);
    if(name == "")
    {
        artDialog.tips("请输入愿望名称");
        return false;
    }
    if(money == "" || !(parseInt(money) == money))
    {
        artDialog.tips("愿望金额必须是整数");
        return false;
    }
    if(description == "")
    {
        artDialog.tips("请填写愿望详情");
        return false;
    }
    if(endtime == "" || !(parseInt(time) == time))
    {
        artDialog.tips("请填写愿望日期");
        return false;
    }
    if(type != "4")
    {
        if(backdis == "2")
        {
            if(txtarea == "")
            {
                artDialog.tips("请填写实物回报");
                return false;
            }
            else
            {
                $("#back_type").val(2);
            }
        }
        if(backdis == "1")
        {
            if(txtarea1 == "")
            {
                artDialog.tips("请填写虚拟回报");
                return false;
            }
            else
            {
                $("#back_type").val(1);
            }

        }
    }
    ispic = false;
    for(var i=0; i < 5 ;i++)
    {
        if(picitem[i] != "")
        {
            ispic = true;
            break;
        }
    }
    if(!ispic)
    {
        artDialog.tips("至少添加一张图片");
        return false;
    }
    if(longitude == "" || latitude == "")
    {
        artDialog.tips("请允许获取地理位置");
        getLocation();
        return false;
    }
    return true;
}

$("#btn-sub").click(function()
{
    if(!Judge())
    {
        return;
    }
    issubmit = $("#issubmit").val();
    if(issubmit == "1")
    {
        return;
    }
    $("#issubmit").val("1");
    $.ajax({
        type: "POST",
        url:"/mywish/releasewish",
        data: $("#form-release").serialize(),
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == "0")
                {

                     location = data.msg;

                }
                else
                {
                     artDialog.tips("愿望发布失败：" + data.msg);
                     $("#issubmit").val("0");
                }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                artDialog.tips("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                 $("#issubmit").val("0");
            }
        });
});

$(document).on("change",".upload-file",function(){
    if(picItem.length <= 0 && curItem == 0)
    {
        artDialog.tips("最多添加5张图片");
        return false;
    }

    if(curItem == 0)
    {
        curItem = picItem.pop();
    }
    var data = new FormData();
        //为FormData对象添加数据
        //
        hasFile = false;
        error="";
        $.each($(this)[0].files, function(i, file) {
        filename = $.md5(file.name);
            if(okPicFiles[filename] === undefined)
            {
                data.append("upload_file", file);
                hasFile = true;
                curPicFile = filename;
            }
            else
            {
                artDialog.tips("该图片已经上传过");
            }
        });
        if(!hasFile)
        {
            return;
        }
        if(curItem <= 0)
        {
            artDialog.tips("状态错误，没有选中图片");
            return;
        }
        dialog = art.dialog({
        title:"请选择上传图片",
        fixed:true,
        lock:true,
        content:"<img src=\"http://image.matewish.cn/backend/loading24.gif\" />",
        ok:false
    });
        $.ajax({
            url:"/mywish/uploadpic?pic_type=wish&token='.$token.'",
            type:"POST",
            data:data,
            cache: false,
            contentType: false,    //不可缺
            processData: false,    //不可缺
            success:function(data)
            {

                data = $.parseJSON(data);
                if(data.code == "0")
                {
                    item = $("#pic-item-template").html();
                    item = item.replace("{src}",data.msg);
                    item = item.replace("{id}",curItem);
                    okPicItem[curItem.toString()]=curItem;
                    $("#pic"+curItem.toString()).val(data.msg);
                    //$("#img-item-contain").prepend(item);
                    $("#upload_file").parent().before(item);
                   okPicFiles[filename]=curItem;
                   okPicFilesIndex[curItem.toString()]=curPicFile;
                    curItem = 0;
                    curPicFile=null;
                    if(picItem.length == 0)
                    {
                        $("#upload_button_content").hide();
                    }
                }
                else
                {
                    artDialog.tips(data.msg);
                }
                if(dialog != null)
                {
                    dialog.close();
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                artDialog.tips("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                //alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                if(dialog != null)
                {
                    dialog.close();
                }
             }
        });
});

$(document).on("click",".item-pic-delete",function(){
        id=$(this).attr("data-id");
        $(this).parent().parent().remove();
        delete okPicItem[id];
        picItem.push(id);
        $("#pic" + id).val("");
        filename = okPicFilesIndex[id];
        delete okPicFiles[filename];
        delete okPicFilesIndex[id];
        if(picItem.length > 0)
        {
            $("#upload_button_content").show();
        }
});

function getLocation()
{
    wx.getLocation({
    type: "wgs84", // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入"gcj02"
    success: function (res) {
        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
        var speed = res.speed; // 速度，以米/每秒计
        var accuracy = res.accuracy; // 位置精度
        $("#longitude").val(longitude);
        $("#latitude").val(latitude);
        }
    });
}
if (typeof window.wx !== undefined)
{
    wx.config({
        debug: false,
        appId: "wxb01ee35e86b5d74a",
        timestamp: 1455695941,
        nonceStr: "meiyuanduo2jd2oDGFERETRE",
        signature: "'.$share_sign.'",
        jsApiList: [
        "onMenuShareTimeline",
        "onMenuShareAppMessage",
        "onMenuShareQQ",
        "onMenuShareWeibo",
        "onMenuShareQZone",
        "openLocation",
        "getLocation"
    ]
    });
    wx.ready(function(){
        getLocation();
        wx.onMenuShareTimeline({
            title: "'.$share['title'].'", // 分享标题
            link: "'.$share['link'].'", // 分享链接
            imgUrl: "'.$share['pic'].'", // 分享图标
            success: function () {
                //alert("分享朋友圈ok");
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
                //alert("取消分享朋友圈ok");
            }
        });
        wx.onMenuShareAppMessage({
            title: "'.$share['title'].'", // 分享标题
            desc: "'.$share['content'].'", // 分享描述
            link: "'.$share['link'].'", // 分享链接
            imgUrl: "'.$share['pic'].'", // 分享图标
            type: "", // 分享类型,music、video或link，不填默认为link
            dataUrl: "", // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
                //alert("tttttt");
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
                //alert("ffffffff");
            }
        });
        wx.onMenuShareQQ({
            title: "'.$share['title'].'", // 分享标题
            desc: "'.$share['content'].'", // 分享描述
            link: "'.$share['link'].'", // 分享链接
            imgUrl: "'.$share['pic'].'", // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });
    wx.error(function(res){
        artDialog.tips(res.errMsg);
        // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。

    });
}

';
$this->registerJs($js);