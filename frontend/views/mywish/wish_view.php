<?php
\common\assets\WxAsset::register($this);
?>
    <style>
    .wrap
    {
        width: 100%;
        padding-top: 8px;
        padding-bottom: 60px;
    }
    .myheader
    {
        position: fixed;
        background-color: #ffffff;
        width: 100%;
        height: 54px;
        top: 0px;
        left: 0px;
        border-top: 1px solid #e5e5e5;
        z-index: 99;
    }
    .footer
    {
        position: fixed;
        background-color: #ffffff;
        width: 100%;
        height: 60px;
        bottom: 0px;
        left: 0px;
        border-top: 1px solid #e5e5e5;
    }
    .footer-contain
    {
        margin: 10px 15px;
    }
    .footer-left
    {
        margin: 12px 0px 0px 30px ;
        float: left;
        clear: left;
    }
    .footer-left-img
    {
        width: 150px;
    }
    .footer-right-img
    {
        width: 75px;
    }
    .footer-right
    {
        margin: 12px 30px 0px 0px ;
        float: right;
        clear: right;
    }
    .img-circle {
        border-radius: 50%;
        width: 40px;
        height: 40px;
    }
    .head-content
    {
        height: 35px;
        padding: 0 5px 5px 10px;
        display: block;
    }
    .pull-left
    {
        float: left !important;
        display: inline-block;
    }
    .wishviewhead
    {
        width: 100%;
        height: 50px;
        background-color: #f4f4f4;
    }
    .info
    {
        width: auto;
        margin-top: 10px;
        margin-left: 15px;
    }
    .namefont
    {
        font-size: 14pt;
        margin-top: -5px;
        font-family: 微软雅黑;
        color: #666;
    }
    .sex
    {
        margin-left: 10px;
        margin-top:7px;
        width: 18px;
        height: 17px;
        clear: right;
    }
    .sex img
    {
        width: 18px;
        height: 17px;
    }
    .container-part1
    {
        width: 100%;
    }
    .container-part1-leftdays
    {
        margin:10px 10px 6px 10px;
        padding: 1px;

    }
    .pull-right
    {
        float: right;
        clear: both;
    }
    .leftdays
    {
        margin-right: 10px;
    }
    .daysleft
    {
        font-size: 13pt;
        display: inline-block;
        vertical-align: middle;
        color: #666;
        font-family: 微软雅黑;
    }
    .leftdaypic
    {
        vertical-align: middle;
        width: 25px;
        height: 25px;
    }

    .part1-money-left
    {
        font-size: 13pt;
        padding-left: 10px;
        color: #666;
        font-family: 微软雅黑;
        text-align: left;
    }
    .part1-money-right
    {
        display: inline-block;
        font-size: 36pt;
    }
    p
    {
        margin: 0px;
        text-align: left;
    }
    .container-part1-title
    {
        margin:10px 10px 6px 10px;
        vertical-align: middle;
        text-align: left;
        font-size: 16pt;
        color:#666;
        font-family: "微软雅黑";
    }
    .container-part1-process
    {
        padding: 10px 0;
        height: 7px;
    }
    .base-process
    {
        height: 7px;
        line-height: 7px;
        width: 100%;
    }
    .part1-process-back
    {
        background-color: #e7e7e7;
    }
    .part1-process-reward
    {
        background-color: #ff5757;
        width: <?= $reward_len ?>;
        height: 7px;
        margin: 0;
    }
    .part1-process-redpacket
    {
        background-color: #ff5757;
        width: <?=$redpacket_len ?>px;
        height: 16px;
    <?= $reward_len <= 0 ? 'clear:left;':''?>
    }
    .container-part1-money
    {
        width: 100%;
        clear: left;
    }
    .process-contain
    {
        /*margin: 0 10px;*/
        vertical-align: middle;
        padding: 0 10px 10px 10px;
    }
    .process-note
    {
        width: 100%;
        height:15px;
        clear: both;
    }
    .process-not-base
    {
        background-size: 80px 52px;
        background-repeat: no-repeat;
        width: 80px;
        height: 52px;
        font-size: 11pt;
        text-align: center;
    }
    .process-note-reward
    {
        background-image: url(http://oss.aliyuncs.com/meiyuannet/wish_web/labeldown.png);
        float: left;
        padding: 3px 0px;
        margin-left: <?=$reward_note_x ?>px;
    }
    .process-note-left
    {
        float: left;
        padding: 3px 0px;
        margin-left: <?=$left_note_x ?>px;
    }
    .process-note-redpacket
    {
        width: 100%;
    }
    .top-bord-contain
    {
        margin: 30px auto;
        text-align: left;
    }

    .top-bord-label
    {
        font-size: 12pt;
        display: inline-block;
        vertical-align: middle;
/*        margin-left: 15px;*/
        padding-left: 10px;
        color: #666;
        font-family: 微软雅黑;
    }
    .top-bord-content
    {
        display: inline-block;
        margin-left: 0px;
        vertical-align: middle;
    }
    .top-hat
    {
        position: absolute;
        margin-top: -15px;
        margin-left: -3px;
    }
    .bord-content
    {
        display: inline-block;
    }
    .imagesort
    {
        width: 30px;
        height: 30px;
        border-radius: 50%;
    }
    .container-part2-top
    {
        margin-top: -18px;
    }
    .container-part2
    {
        width: 100%;
        height: auto;
    }
    .part2-title-back
    {
        width: 100%;
        height: 36px;
        background-color: #f4f4f4;
        border-top: 1px solid #e5e5e4;
        border-bottom: 1px solid #e5e5e5;
        line-height: 36px;
    }
    .part2-title
    {
        font-size: 14pt;
        margin: 0px 15px;
        height: 36px;
        font-family: "微软雅黑";
        /*font-weight: lighter;*/
        color: #666;
    }
    .part2-content
    {
        width: 100%;
        margin-top: 20px;
    }
    .content-discription
    {
        margin: 0px 30px 10px 15px;
        line-height: 1.5;
        font-size: 14px;
        font-family: "微软雅黑";
        /*font-weight: lighter;*/
        color: #666;
        text-align: justify;
    }
    .content-pic
    {
        width: 100%;
        max-width: 100%;
        text-align: center;
    }
    .wishimg
    {
        margin: 10px 10px 0px 10px;
    }
    .wishimg-header
    {
        margin-top: 55px;
        width: 100%;
    }
    .comment-item
    {
        border-bottom: 1px solid #e5e5e5;
    }
    .item-left
    {
        float: left;
        width: 50px;
        display: inline-block;
        clear: left;
    }
    .item-right
    {
        display: inline-block;
        margin-top: 10px;
        margin-left: 10px;
    }
    .imagecomment
    {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin-left: 10px;
        margin-top: 10px;
    }
    .splite-packet
    {
        border-top: 0px;
        border-left: 0px;
        border-right: 0px;
        border-bottom:  1px solid #e5e5e5;
    }
    .item-right-top-base
    {
        display: inline-block;
    }
    .item-right-top-name
    {
        color: #141414;
        font-size: 12pt;
    }

    .item-right-top-money
    {
        color: #666666;
        font-size: 12pt;
    }
    .item-right-top-time
    {
        float: right;
        color: #666666;
        font-size: 10pt;
        margin-right: 5px;
        margin-top: 2px;
    }
    .item-right-body
    {
        margin:  10px 0px;
        font-size: 12pt;
        color: #666666;
    }
    .item-right-bottom-reward-money
    {
        display: inline-block;
        font-size: 12pt;
        color: #141414;
    }
    .item-right-bottom-packet-money
    {
        display: inline-block;
        font-size: 12pt;
        color: #141414;
    }
    .item-right-bottom
    {
        margin-bottom: 5px;
    }

    .z_01{
        float: left;
        color: #4a4a4a;
    }

    .z_02{
        float: right;
        margin-right: 10px;
        color: #4a4a4a;

    }

    .linr{
        width: 100%;
        height: 14px;
        background-color: #f4f4f4 ;
        margin-top: 60px;
    }

    .k_01{
        width: 100%;
        height: 96px;
        background-color: #fff;

    }

    .miro{

        height: 40px;
        text-align: center;
        background-color: #ff5757;
        border-radius: 7px;
        line-height: 40px;
        font-size: 20px;
        text-decoration: none;
        color: #fff;;
        width: 100%;
        border: none;
    }




    body{
        overflow-x: hidden;
    }


    </style>
    <header class="myheader">
        <div class="footer-left">
            <p>
                <img class="footer-left-img" src="http://image.matewish.cn/wish_web/icon1.png" alt="下载">
            </p>
        </div>
        <div class="footer-right">
            <p>
                <a href="http://www.matewish.cn/download.html" target="_blank"><img class="footer-right-img" src="http://image.matewish.cn/system/download.png" alt="下载"></a>
            </p>
        </div>
    </header>
    <img class="wishimg-header" src="<?= $wish_detail['pic1'] ?>@!picwx2" alt="愿望图片">
    <div class="container-part1-title">
        <p><?= $wish_detail['wish_name'] ?></p>
    </div>
    <div class="head-content">
        <div class="pull-left">
            <img src="<?= (empty($wish_detail['user_pic'])?'http://oss.aliyuncs.com/meiyuannet/wish_web/defaultpic.png':$wish_detail['user_pic'])?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p class="namefont"><?= $wish_detail['publish_user_name'] ?></p>
        </div>
        <div class="pull-left sex">
            <img src="http://oss.aliyuncs.com/meiyuannet/wish_web/sex<?=($wish_detail['sex'] === '男'?'1':'2') ?>.png"  alt="User Image">
        </div>
    </div>
    <div class="wrap">
    <div class="container-part1">
<!--        <div class="container-part1-leftdays">-->
            <div class="pull-right leftdays">
                <p class="daysleft"><?=($left_days < 0 ?'已经过期':sprintf('剩余%s天',$left_days))?></p>
            </div>
<!--        </div>-->
        <div class="container-part1-money">
            <div class="part1-money-left"><p>期望金额￥<?= round($wish_detail['wish_money'],2) ?></p></div>
        </div>
        <div class="process-contain">
            <div class="container-part1-process">
                <div class="base-process part1-process-back">
                    <div class="base-process part1-process-reward">&nbsp;</div>
                </div>
            </div>
            <div class="process-note">
                <div class="process-not-base process-note-redpacket">

                    <span class="z_01"><?=$reward_len ?></span>
                    <span class="z_01">打赏 ￥<?= round($wish_detail['ready_reward_money'],2) ?></span>
                    <span class="z_01">奖励 ￥<?= round($wish_detail['red_packets_money'],2) ?></span>
                    <span class="z_02"><?=$wish_detail['reward_num']?>次打赏</span>

                </div>

            </div>
        </div>
        <div class="top-bord-contain">
            <div class="top-bord-label">
                <p>打赏土豪榜:</p>
            </div>
            <div class="top-bord-content">
                <?php if(isset($wish_detail['reward_max_list']) &&
                    is_array($wish_detail['reward_max_list']) &&
                    count($wish_detail['reward_max_list']) > 0
                )
                {
                    $len = count($wish_detail['reward_max_list']);
                    for($i = 0; $i < $len; $i ++)
                    {
                        if($i > 4)
                        {
                            break;
                        }
                        ?>
                        <div class="bord-content">
                            <img class="top-hat" src="http://oss.aliyuncs.com/meiyuannet/wish_web/matewish_<?= strval($i + 1) ?>.png"
                                 alt="第一名"/>
                            <img class="imagesort" src="<?= empty($wish_detail['reward_max_list'][$i]['user_pic'])?'http://oss.aliyuncs.com/meiyuannet/wish_web/defaultsort.png': $wish_detail['reward_max_list'][$i]['user_pic'] ?>"
                                 alt="第一土豪">
                        </div>
                    <?php
                    }
                } ?>
                <!--                <div class="bord-content">
                                    <img class="top-hat" src="http://oss.aliyuncs.com/meiyuannet/wish_web/matewish_1.png" alt="第一名"/>
                                    <img class="imagesort" src="http://oss.aliyuncs.com/meiyuannet/wish_web/defaultsort.png" alt="第一土豪">
                                </div>
                                <div class="bord-content">
                                    <img class="top-hat" src="http://oss.aliyuncs.com/meiyuannet/wish_web/matewish_2.png" alt="第一名"/>
                                    <img class="imagesort" src="http://oss.aliyuncs.com/meiyuannet/wish_web/defaultsort.png" alt="第一土豪">
                                </div>
                                <div class="bord-content">
                                    <img class="top-hat" src="http://oss.aliyuncs.com/meiyuannet/wish_web/matewish_3.png" alt="第一名"/>
                                    <img class="imagesort" src="http://oss.aliyuncs.com/meiyuannet/wish_web/defaultsort.png" alt="第一土豪">
                                </div>
                                <div class="bord-content">
                                    <img class="top-hat" src="http://oss.aliyuncs.com/meiyuannet/wish_web/matewish_4.png" alt="第一名"/>
                                    <img class="imagesort" src="http://oss.aliyuncs.com/meiyuannet/wish_web/defaultsort.png" alt="第一土豪">
                                </div>
                                <div class="bord-content">
                                    <img class="top-hat" src="http://oss.aliyuncs.com/meiyuannet/wish_web/matewish_5.png" alt="第一名"/>
                                    <img class="imagesort" src="http://oss.aliyuncs.com/meiyuannet/wish_web/defaultsort.png" alt="第一土豪">
                                </div>-->
            </div>
        </div>
    </div>
    <div class="container-part2 container-part2-top">
        <div class="part2-title-back">
            <div class="part2-title">
                <p>愿望描述</p>
            </div>
        </div>
        <div class="part2-content">
            <div class="content-discription">
                <p><?=$wish_detail['discribtion'] ?></p>
            </div>
            <div class="content-pic">
                <?php
                for($i = 1; $i <= 6; $i ++)
                {
                    if($i === 1)
                    {
                        continue;
                    }
                    $key = 'pic'.strval($i);
                    if(isset($wish_detail[$key]) && !empty($wish_detail[$key]))
                    {


                        ?>
                        <img class="wishimg" src="<?= $wish_detail[$key] ?>" alt="愿望图片">
                    <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <?php if(!empty($wish_detail['back_dis']))
    {
        ?>
        <div class="container-part2">
            <div class="part2-title-back">
                <div class="part2-title">
                    <p>我的回报</p>
                </div>
            </div>
            <div class="part2-content">
                <div class="content-discription">
                    <p><?= $wish_detail['back_dis'] ?></p>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="container-part2">
        <?php
        if(isset($comment_list) && is_array($comment_list) && count($comment_list) > 0)
        {
            ?>
            <div class="part2-title-back">
                <div class="part2-title">
                    <p>评论</p>
                </div>
            </div>
            <?php

            foreach($comment_list as $comment)
            {
                $dataType = $comment['data_type'];
                $content = $comment['content'];
                $content = str_replace("\r",'',$content);
                $content = str_replace("\n", '<br>',$content);
                if($dataType == '1') //1 评论  2 打赏
                {
                    ?>
                    <div class="comment-item">
                        <div class="item-left">
                            <img class="imagecomment" src="<?= (!empty($comment['pic'])?$comment['pic']:'http://oss.aliyuncs.com/meiyuannet/wish_web/defaultpic.png') ?>"
                                 alt="头像">
                        </div>
                        <div class="item-right">
                            <div class="item-right-top">
                                <div class="item-right-top-base item-right-top-name"><p><?= $comment['user_name'] ?></p></div>
                                <?php
                                if(!empty($comment['content_title']))
                                {
                                    ?>
                                    <div class="item-right-top-base item-right-top-money"><p>回复</p></div>
                                    <div class="item-right-top-base item-right-top-name">
                                        <p><?= $comment['content_title'] ?></p></div>
                                <?php
                                }
                                ?>
                                <div class="item-right-top-base item-right-top-time"><p><?= date('Y-m-d H:i',strtotime($comment['create_time']))?></p></div>
                            </div>
                            <div class="item-right-body">
                                <p>
                                    <?= $content?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php
                }
                else {
                    ?>
                    <div class="comment-item">
                        <div class="item-left">
                            <img class="imagecomment" src="<?= (!empty($comment['pic'])?$comment['pic']:'http://oss.aliyuncs.com/meiyuannet/wish_web/defaultpic.png') ?>"
                                 alt="头像">
                        </div>
                        <div class="item-right">
                            <div class="item-right-top">
                                <div class="item-right-top-base item-right-top-name"><p><?= $comment['user_name'] ?></p></div>
                                <div class="item-right-top-base item-right-top-money"><p>+<?= round($comment['content_title'],2) ?>元</p></div>
                                <div class="item-right-top-base item-right-top-time"><p><?=date('Y-m-d H:i',strtotime($comment['create_time'])) ?></p></div>
                            </div>
                            <div class="item-right-body">
                                <p>
                                    <?= $content?>
                                </p></div>
                            <?php
                            if (doubleval($comment['red_packets_money']) > 0)
                            {
                                ?>
                                <hr class="splite-packet"/>
                                <div class="item-right-bottom">
                                    <div class="item-right-bottom-reward-money"><p><?= $comment['reward_money_except_packets'] ?>元（打赏）</p></div>
                                    <div class="item-right-bottom-packet-money"><p><?= $comment['red_packets_money']?>元（<?= ($comment['is_base_verify'] == '1')?'奖金':'待验证' ?>）</p></div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php
                }
                ?>


            <?php
            }
        }
        ?>
    </div>
<!--    <div class="linr">&nbsp;</div>-->
    </div>

    <footer class="footer">
        <div class="footer-contain">
<!--        <div class="k_01">-->
                <a href="http://<?=$_SERVER['HTTP_HOST']?>/mywish/rewardshow/?token=<?=$token?>"><button class="miro">打赏</button></a>
        <!--</div>-->
        </div>
    </footer>
<?php

$js='
$(function(){
deviceWidth = $(window).width();
$(".wishimg").css("width",deviceWidth - 20);
$(".item-right").css("width",deviceWidth - 50-10);
});

if (typeof window.wx !== undefined) {
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
        "startRecord",
        "stopRecord",
        "onVoiceRecordEnd",
        "playVoice",
        "pauseVoice",
        "stopVoice",
        "onVoicePlayEnd",
        "uploadVoice",
        "downloadVoice",
        "chooseImage",
        "previewImage",
        "uploadImage",
        "downloadImage",
        "translateVoice",
        "getNetworkType",
        "openLocation",
        "getLocation",
        "hideOptionMenu",
        "showOptionMenu",
        "hideMenuItems",
        "showMenuItems",
        "hideAllNonBaseMenuItem",
        "showAllNonBaseMenuItem",
        "closeWindow",
        "scanQRCode",
        "chooseWXPay",
        "openProductSpecificView",
        "addCard",
        "chooseCard",
        "openCard"
    ]
    });
wx.ready(function(){
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
    alert(res.errMsg);
    // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。

});

}
';
$this->registerJs($js,\yii\web\View::POS_END);
?>