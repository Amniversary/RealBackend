<h1><?=$sign?></h1>
<a href="http://mp.weixin.qq.com/mp/redirect?url=https%3A%2F%2Fitunes.apple.com%2Fcn%2Fapp%2Fmei-yuan%2Fid1080702692%3Fmt%3D8" target="_blank"><img class="footer-right-img" src="http://image.matewish.cn/system/download.png" alt="下载"></a>
<?php
\common\assets\WxAsset::register($this);
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/17
 * Time: 17:38
 */
$js='
if (typeof window.wx !== "undefined") {
wx.config({
        debug: false,
        appId: "wxb01ee35e86b5d74a",
        timestamp: 1455695941,
        nonceStr: "meiyuanduo2jd2oDGFERETRE",
        signature: "'.$sign.'",
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
    alert("ok");
wx.onMenuShareTimeline({
    title: "'.$share['title'].'", // 分享标题
    link: "'.$share['link'].'", // 分享链接
    imgUrl: "'.$share['pic'].'", // 分享图标
    success: function () {
        alert("分享朋友圈ok");
    },
    cancel: function () {
        // 用户取消分享后执行的回调函数
        alert("取消分享朋友圈ok");
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
        alert("tttttt");
    },
    cancel: function () {
        // 用户取消分享后执行的回调函数
        alert("ffffffff");
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