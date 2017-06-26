<style>
    .content-header{
        display: none;
    }
    .content{
        margin-top:0 !important;
        background: #f8f8f8 url(http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/poster/bg.jpg) repeat top left;
    }
    ul{
        padding: 0;
        margin: 0;
    }
    .top{
        height: 45px;
    }
    .user-form{
        width: 80%;
        margin: 0 auto;
        font-size: 16px;
        border-radius: 5px;
        overflow: hidden;
    }
    .col{
        display: inline-block;
        border: 2px solid #3c8dbc;
        width: 33.3333%;
        text-align: center;
        float: left;
    }
    .bg-black{
        background-color: #3c8dbc !important;
    }
    .ft-white{
        font-size: #fff;
    }
    /*.bottom{*/
        /*border: 1px solid #000;*/
        /*height: 1000px;*/
    /*}*/
    /*.bottom ul{*/
        /*width: 90%;*/
        /*padding: 3% 0;*/
        /*margin: auto;*/
    /*}*/
    /*.bottom ul li{*/
        /*width: 20%;*/
        /*display: block;*/
        /*position: relative;*/
        /*float: left;*/
        /*padding: 1%;*/
        /*font-size: 16px;*/
    /*}*/
    /*.bottom .user-pic img{*/
        /*width: 100%;*/
        /*max-height:200px;*/
    /*}*/
    .pic-script{
        display: block;
    }
    .view-larger{
        float: left;
        display: inline-block;
        width: 50%;
        text-align: center;
        padding: 5px 0;
        border: 1px solid #3c8dbc;
        border-right: 0 !important;
        cursor: pointer;
        border-bottom-left-radius: 5px;
    }
    .delete{
        float: left;
        display: inline-block;
        width: 50%;
        padding: 5px 0;
        text-align: center;
        border: 1px solid #3c8dbc;
        cursor: pointer;
        border-bottom-right-radius: 5px;
    }
    .box {
        margin-bottom: 20px;
        float: left;
        width: 240px;
    }
    .box img {
        max-width: 100%
    }
    .container-fluid {
        padding: 20px;
        border-right: 2px solid #3c8dbc;
        border-left: 2px solid #3c8dbc;
        border-bottom: 2px solid #3c8dbc;
    }
    .font-pd{
        padding: 0 10px;
    }
</style>
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/9
 * Time: 14:54
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

\common\assets\ArtDialogAsset::register($this);


?>

<div class="user-form">
    <div class="top">
        <div class="col">
            <div class="user-id bg-black ft-white">用户id</div>
            <div class="font-pd"><?= $dataProvider['0']['user_id'] ?></div>
        </div>
        <div class="col">
            <div class="client-no bg-black ft-white">密播id</div>
            <div class="font-pd"><?= $dataProvider['0']['client_no'] ?></div>
        </div>
        <div class="col">
            <div class="nick-name bg-black ft-white">用户昵称</div>
            <div class="font-pd"><?= $dataProvider['0']['nick_name'] ?></div>
        </div>
    </div>
    <div id="masonry" class="container-fluid">
            <?php foreach($dataProvider as $a) { ?>
                <div class="box">
                    <img src="<?= $a['pic'] ?>">
                    <div class="pic-script">
                        <div class="view-larger">查看大图</div>
                        <div class="delete" id="<?= $a['dynamic_id'] ?>">删除</div>
                    </div>
                </div>
            <?php } ?>
    </div>
</div>
<?php
$js = '
    $("body").on("click",".view-larger",function(){
        var img = $(this).parent().parent().find("img").attr("src");
        if(img != ""){
            art.dialog({
                content: "<img id=\"pic\" style=\"width:640px;\" src=\" "+ img + " \">",
                title:"用户图片",
                cancelVal: "关闭",
                cancel: true //为true等价于function(){}
            });

            if($("#pic").height() > 700)
            {
                var ratio = 700/$("#pic").height();
                var pic_width = $("#pic").width() * ratio;
                $("#pic").height("700px");
                $("#pic").width(pic_width);
            }
        }
    });

    $("body").on("click",".delete",function(){
    var $url = "/clientalbum/delete?dynamic_id="+$(this).attr("id")+"&type=viewall";

    art.dialog.confirm("你确定要删除这张图片吗？", function () {
         $.ajax({
        type: "POST",
        url: $url,
        success: function(data)
            {
                window.location.reload()
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
             }
        });
    }, function () {
    });

});


$(function() {
    var $container = $("#masonry");
    $container.imagesLoaded(function() {
        $container.masonry({
                itemSelector: ".box",
                gutter: 20,
                isAnimated: true,
            });
     });
});
';
$this->registerJs($js,\yii\web\View::POS_END);
