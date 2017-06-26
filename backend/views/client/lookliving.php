<?php
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

?>
<script src="http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/jwplayer.js"></script>
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

<div id="container"></div>

<script type="text/javascript">
    var playerInstance = jwplayer("container");
    var thePlayer= playerInstance.setup({
        flashplayer: 'http://bmpic.matewish.cn/meibo-test/js/jwplayer.flash.swf',
        //androidhls: true,
        //file: "http://7j1zn6.com1.z0.glb.clouddn.com/jq22com.mp4",
        //image: "//support-static.jwplayer.com/images/streaming/usinghls-clock.png",
        //title:"The JW Clock",
        width: '512px',
        /*height:'180px',*/
        /*aspectratio: '16:9',*/
        // fallback:false,
        //primary: 'flash',
            autostart:true,//自动播放

        'sources': [
                {
                    'file':'http://pullhls1.live.126.net/live/fa2ccdf5c59d44a48d1c7b054a94df4b/playlist.m3u8'
                },
                {
                    'file':'rtmp://v1.live.126.net/live/fa2ccdf5c59d44a48d1c7b054a94df4b'
                },
                {
                    'file':'http://v1.live.126.net/live/fa2ccdf5c59d44a48d1c7b054a94df4b.flv'
                }
        ]
    });
    thePlayer.onPlay(function(){
        $('.jwplayer').removeClass('jw-state-buffering').addClass('jw-state-playing');
    })

</script>


