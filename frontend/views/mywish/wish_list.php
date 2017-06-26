<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 13:41
 */
\common\assets\ArtDialogAsset::register($this);
\common\assets\WxAsset::register($this);
?>
<style>
    li {
        list-style-type:none;
        background-repeat:no-repeat;
        background-size:100% auto;
        height:780px;
        margin:10px;
        position:relative;
    }

    *{
        padding:0;
        margin:0;
        font-family: "微软雅黑", arial, sans-serif;

    }

    nav{
        width:100%;
        line-height:50px;
        height:50px;
        font-family:normal 100% "微软雅黑";
        text-align:center;
    }
    .day{
        padding-right: 8px;
    }

    nav a{
        text-decoration:none;
        padding:50px 0;
    }

    .nav_1 a:hover,a:focus{
        color:#ff5959;
    }
    .nav_1{
        width:49.5%;
        float:left;
        font-size:380%;
        border-bottom:inset;
        border-color:#ff5959;
    }
    .nav_2{
        width:49.5%;
        float:right;
        font-size:380%;
        color: #000;
    }


    .sex{
        height:35px;
        width:35px;
    }


    .nav_1,.nav_2 a:hover{
        color:#ff5959;
    }

    .banner img{
        max-width:100%;
    }

    .content{
        margin:auto;
        width:100%;
        background-color:#FFF;
    }

    .content ul{
        padding:0 5px;
    }

    .jz{
        margin: 10px;
        float: left;
        display: block;
        height: 250px;
        width: 9%;
        border-radius:50%;
        vertical-align:middle;
        border: 3px solid #ff5959;
        overflow:hidden;
        line-height:50px;
    }

    .center_top_w{
        display:inline;
        width:100%;
    }

    .center_top_w .name{
        padding-top:50px;
        padding-bottom:60px;
        font-size:260%;
        color:#FFF;
        display:inline-block;
    }

    .xin{
        float:right;
        padding-top:20px;
        padding-right:30px;
        width:15%;
    }

    .center_top_w .length{
        font-size:260%;
        color:#FFF;
    }

    .center_bottom{
        height:140px;
        background-color:#333;
        width:100%;
        z-index:101;
        background:rgba(0,0,0,0.3);
        position:absolute;
        left:0;
        bottom:0;
        border-bottom-left-radius: 2px;
        border-bottom-right-radius: 2px;
    }

    .center_bottom_nr{
        z-index:100;
    }
    .center_title{
        font-size:260%;
        float:left;
        color:#FFF;
        width:100%;
        padding-left:10px;
        padding-bottom:10px;
    }

    .center_time{
        display:inline-block;
        padding-left:10px;
        color:#FFF;
        font-size:260%;
    }

    .center_money{
        font-size:260%;
        color:#FFF;
        float:right;
        position: absolute;
    }

    .footer{
        width:80%;
        margin:20px auto;
    }

    #domeForm legend{
        font-size:24px;
        font-family:normal 100% "微软雅黑";
    }

    .footer p{
        margin:10px auto;
        width:80%;
        display:inline-block;
    }

    .footer fieldset{
        text-align:center;
    }


    .footer label{
        display:inline-block;
        float:left;
        padding-bottom:10px;
        padding-left:10px;
    }

    .footer input,button{
        height:35px;
        min-width:180px;
        font-weight:bold;
        border:1px solid #999;
    }


    .footer label.error{
        margin-left:10px;
        color:red;
    }

    #desire{
        height:100px;
        min-width:180px;
        border:1px solid #999;
    }

    #desire.error{
        border:1px solid #F00;
    }

    input.error{
        border:1px solid #F00;
    }

    input[type=submit],button{
        margin-top:10px;
        font-size:22px;
        padding:10px 10px;
    }

    #b1{
        font-size:22px;
    }

    @media screen and (min-width:300px) {
        nav a{
            padding:0;
        }
        .center_top .people{
            width:100%;
            height:100%;
            margin-bottom: 30px;
        }
        #loadgif{
            left: 38%;
        }
        .nav_1:after{
            position:absolute;
            content:'|';
            display:block;
            top: 1%;
            right:50%;
            font-size: 20px;
            color: #ccc;
            -webkit-transform:scale(1,1.5);
        }
        .jz{
            height:30px;
            width: 9%;
            border:2px solid #ff5959;
            line-height:50px;
        }
        .nav_1{
            font-size:16px;
        }
        .nav_2{
            font-size: 16px;;
        }
        li{
            height:160px;
        }
        .sex{
            height:10px;
            width:10px;
        }

        .center_top_w .name{
            font-size:90%;
            padding-bottom:5px;
            padding-top:10px;
            margin:0;
        }
        .center_top_w .length{
            font-size:60%;
        }
        .xin{
            width:8%;
            padding-top: 10px;
            padding-right: 10px;
        }
        .center_bottom{
            height:50px;
        }
        .center_title{
            padding:3px 0 3px 10px;
            font-size: 16px;
        }
        .center_time{
            font-size:12px;
        }
        .center_money{
            font-size:20px;
            right: 10px;
            top: 15px;
        }
    }

    @media screen and (min-width:400px) {
        nav a{
            padding:0;
        }
        .nav_1{
            font-size:16px;
            border-right: inset;
            border-right-color: #ff5959;
            border-right: 1px;
        }
        .nav_2{
            font-size:16px;
        }
        #loadgif{
            left: 38%;
        }
        .jz{
            margin: 10px;
            height: 35px;
            width: 9%;
            border:1px solid #ff5959;
            line-height:35px;
        }
        .sex{
            height:12px;
            width:12px;
        }
        li{
            height:205px;
        }
        .center_top_w .name{
            font-size:100%;
            padding-bottom:5px;
            padding-top:10px;
        }
        .center_top_w .length{
            font-size:80%;
        }
        .xin{
            width:9%;
        }
        .center_bottom{
            height:60px;
        }
        .center_title{
            font-size: 16px;
        }
        .center_time{
            font-size:12px;
        }
        .center_money{
            font-size:20px;
            right: 10px;
        }
    }

    @media screen and (min-width:700px){
        nav a{
            padding:10px 0;
        }
        .nav_1:after{
            top:2%;
        }
        .xin{
            padding-top: 30px;
            padding-right: 30px;
        }

        .nav_1{
            font-size:320%;
        }
        .nav_2{
            font-size:320%;
        }
        .jz{
            margin: 30px;
            height: 75px;
            width: 10%;
            border: 5px solid #ff5959;
            line-height:75px;
        }
        .sex{
            height:20px;
            width:20px;
        }
        #loadgif{
            left: 50%;
        }
        li{
            height:330px;
        }
        .center_top_w .name{
            font-size:160%;
            padding-bottom:20px;
            padding-top:30px;
        }
        .center_top_w .length{
            font-size:130%;
        }
        .center_bottom{
            height:80px;
        }
        .center_title{
            font-size:160%;
        }
        .center_time{
            font-size:130%;
        }
        .center_money{
            right: 15px;
            top:26px;
            font-size:260%;
        }
    }

    @media screen and (min-width:1600px){
        nav a{
            padding:40px 0;
        }
        #loadgif{
            left: 50%;
        }
        .xin{
            padding-top: 45px;
            padding-right: 45px;
        }
        .nav_1{
            font-size:380%;
        }
        .nav_2{
            font-size:380%;
        }
        li{
            height:780px;
        }
        .center_top .people{
            width:100%;
            height:100%;
        }
        .jz{
            margin: 40px;
            height: 200px;
            width: 10%;
            border: 10px solid #ff5959;
            line-height:200px;
        }
        .center_top_w .name{
            font-size:360%;
            padding-bottom:40px;
            padding-top:70px;
        }
        .center_top_w .length{
            font-size:300%;
        }
        .sex{
            height:45px;
            width:45px;
        }
        .center_bottom{
            height:140px;
        }
        .center_title{
            font-size:260%;
            padding:20px 0 20px 10px;
        }
        .center_time{
            font-size:200%;
        }
        .center_money{
            font-size:360%;
            right: 20px;
            top:40px;
        }
    }
    
     .swipe{position:relative;visibility:hidden;overflow:hidden;width:100%}
     .swipe-wrap{position:relative;overflow:hidden}
     .swipe-wrap>div{position:relative;float:left;width:100%;background:#ffc}
     .swipe-wrap>div img{display:block;width:100%;margin:0 auto}
     #position{position:absolute;bottom:8px;width:100%;text-align:center}
     #position li{display:inline-block;width:7px;height:7px;margin:0 2px;cursor:pointer;border-radius:8px;background:#dcdcdc}
     #position li.on{background-color:#ff5959}
</style>

<script src="http://image.matewish.cn/app-wishlist/swipe.js"></script>
<div class="top">
    <nav>
        <a class="nav_1" href="#">推荐</a>
        <a class="nav_2" href="#">已实现</a>
    </nav>
    <div id='slider' class='swipe'>
        <div class='swipe-wrap'>
            <div>
                <img src="http://image.matewish.cn/app-wishlist/banner1.jpg">
            </div>
            <div>
                <img src="http://image.matewish.cn/app-wishlist/banner2.jpg">
            </div>
            <div>
                <img src="http://image.matewish.cn/app-wishlist/banner3.jpg">
            </div>
        </div>
        <ul id="position">
            <li class="on"></li>
            <li class=" "></li>
            <li class=" "></li>
        </ul>
    </div>

</div>
<form id="form-submit">
    <input type="hidden" id="longitude" name="Wish[longitude]">
    <input type="hidden" id="latitude" name="Wish[latitude]">
</form>
<div class="content" id="wish_content">

</div>
<div id="loadgif" style="width:66px;height:66px;position:absolute;top:50%;left:37%;">
    　　<img  alt="加载中..." src="http://image.matewish.cn/app-wishlist/loading.gif"/>
</div>

<script type="text/javascript" id="aaa">
    <li style="background-image:url(@bg);border-radius:2px" onclick="location.href='@link'">
            <div class="center_top">
            <div class="jz"><span><a href="#"><img class="people" src="@user"/></a></span></div>
    <div class="center_top_w">
            <p class="name">@name</p>
    <img class="sex" src="@sex" alt=""/>
            <img class="xin"  src="@xin"/>
            <p class="length"><span class="distance">@dis</span></p>
    </div>
    </div>
    <div class="center_bottom">
            <div class="center_bottom_nr">
            <div class="center_title">@title</div>
    <div class="center_time"><span class="day">@days</span>&nbsp;<span class="num">@count</span>人次打赏</div>
    <div class="center_money"><span class="money">@money</span>元</div>
    </div>
    </div>
    </li>
</script>

<?php
$js = '
var listData = [];
var tempate="";
var listcontent="";
var page_ok = 1;
var page_recommend = 1;
var type = 1;// 1 推荐  2 已实现
var page_size = 5;
var page_max = 9999;
var latitude;
var longitude;
var isWx = '.($isWx ? 'true':'false').';
$(function(){
        tempate = $("#aaa").html();
        listData[1] = "";
        listData[2]="";
        //点击“已实现”链接触发
        $(".nav_2").click(function(){
            $(".nav_1").css({"border-bottom":"none", "color":"#000"});
            $(".nav_2").css({"border-bottom":"inset", "border-bottom-color":"#ff5959", "color":"#ff5959"});
            $("#wish_content").empty();
            listData[type]="";
            $("#loadgif").show();
            page_ok=1;
            type = 2;
			page_max = 9999;
            loadData(type,page_ok);
        });

        //点击“推荐”链接触发
        $(".nav_1").click(function(){
            $(".nav_2").css({"border-bottom":"none", "color":"#000"});
            $(".nav_1").css({"border-bottom":"inset", "border-bottom-color":"#ff5959", "color":"#ff5959"});
            $("#wish_content").empty();
            listData[type]="";
            $("#loadgif").show();
            page_ok=1;
            type = 1;
			page_max = 9999;
            loadData(type,page_ok);
        });
        if(!isWx)
        {
            loadData(type, page_ok);
        }
});

//轮播图
window.mySwipe = new Swipe(document.getElementById("slider"), {
        startSlide: 0,
        speed: 400,
        auto: 3000,
        continuous: true,
        disableScroll: false,
        stopPropagation: true,
        callback: function(pos) {
            var i = bullets.length;
            while (i--) {
                bullets[i].className = "";
            }
            bullets[pos].className = "on";
        }
    });
var bullets = document.getElementById("position").getElementsByTagName("li");

$(window).scroll(function(){

      var scrollTop = $(this).scrollTop();
      var scrollHeight = $(document).height();
      var windowHeight = $(this).height();
      if(scrollTop + windowHeight == scrollHeight)
      {
          page_ok++;
          if(page_max >= page_ok)
        {
           loadData(type, page_ok);
        }
    }
});


function loadData(data_type, page)
    {
        url = "http://front.matewish.cn/mywish/wishrequest?page="+ page +"&type=" + data_type;
        $.ajax({
                type: "post",
                url: url,
                cache: false,
                async: true,
                global: false,
                data: $("#form-submit").serialize(),
                success: function (data) {
                $("#loadgif").hide();
                data = $.parseJSON(data);
                len = data.length;
                tmpContent = "";

            if(5 > len){
                page_max = page_ok;
            }
            for(i = 0; i < len; i ++)
            {
                e = data[i];
                user  = e["pic"];
                name = e["nick_name"];
                sex = e["sex"];
                xin = e["other_id"];
                dis = e["distance"];
                title = e["wish_name"];
                days = e["wish_status"];
                count = e["reward_num"];
                money = e["wish_money"] * 1;
                link = e["link"];
                max_count = e["count"];
                bg = e["pic1"];
            if(sex == "女"){
                sex = "http://image.matewish.cn/app-wishlist/female-1.png";
            }else{
                sex = "http://image.matewish.cn/app-wishlist/man.png";
            }
            if(xin == 0){
                xin = "http://image.matewish.cn/app-wishlist/heart.png";
            }else{
                xin = "http://image.matewish.cn/app-wishlist/xinx.png";
            }
                item = tempate;
                item = item.replace("@link",link);
                item = item.replace("@user",user);
                item = item.replace("@bg",bg);
                item = item.replace("@name",name);
                item = item.replace("@sex",sex);
                item = item.replace("@xin",xin);
                item = item.replace("@dis",dis);
                item = item.replace("@title",title);
                item = item.replace("@days",days);
                item = item.replace("@count",count);
                item = item.replace("@money",money);
                tmpContent += item;
            }
            if(len === 5)
            {
                listData[type] += tmpContent;
                $("#wish_content").html(listData[type]);
            }
            else
            {
                 if(len < 5)
            {
                listData[type] += tmpContent;
                    $("#wish_content").html(listData[type]);
            }
            else
            {
                $("#wish_content").html(listData[type] + tmpContent);
            }
        }
    },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
            }
            });
        }


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
        loadData(type,page_ok);
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