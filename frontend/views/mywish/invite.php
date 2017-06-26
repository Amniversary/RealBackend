<style>
.invite-header
{

}
.invite-pic
{
    margin: 80px auto 20px auto;
    vertical-align: middle;
    text-align: center;
}
.invite-name
{
    text-align: center;
    font-size: 36pt;
    font-family: "微软雅黑", arial, sans-serif;
    margin-bottom: 40px;
}
    .img-self
    {
        border-radius: 50%;
        width: 240px;
        height: 240px;
    }
    .seprate
    {
        border-bottom: 1px solid #ff5757;
    }
    .content
    {
        margin: 100px auto;
        font-size: 36pt;
        font-family: "微软雅黑", arial, sans-serif;
        color: #ff5757;
        text-align: center;
    }
    .my-rule
    {
        margin: 140px auto 0px auto;
        font-size: 28pt;
        color: #666666;
        font-family: "微软雅黑", arial, sans-serif;
    }
    .rule-inline
    {
        display: inline-block;
        font-size: 28pt;
        color: #666666;
        font-family: "微软雅黑", arial, sans-serif;
    }
    .rule-inline-left
    {
        width: 7%;
        vertical-align: top;
    }

    .rule-inline-right
    {
        width: 93%;
    }
    .rule-contain
    {
        height: 100px;
    }
    p{
        margin: 0px;
    }

    .imgdownload
    {
        width: 100%;
    }

.my-bg
{
    float: left;
    margin-top: -8px;
    background: url(http://oss.aliyuncs.com/meiyuannet/wish_web/bg.jpg) no-repeat top;
    background-size: 100% 100%;
    width: 100%;
    height: 800px;
    clear: both;
}
.wishviewmain
{
    width: 800px;
    margin: 0px auto;
}
    .bottom-contain
    {
        width: 800px;
        margin: 0 auto;
    }
.footer
{
    position: fixed;
    background-color: #ffffff;
    width: 100%;
    height: 140px;
    bottom: 0px;
    left: 0px;
    border-top: 1px solid #e5e5e5;
}
    .footer-left
    {
        margin: 28px 0px 0px 30px ;
        float: left;
        clear: left;
    }
    .footer-right
    {
        margin: 40px 30px 0px 0px ;
        float: right;
        clear: right;
    }
</style>
<div class="wishviewmain">
<header class="invite-header">
    <div class="invite-pic">
        <img class="img-self" src="<?= (empty($user->pic)?'http://oss.aliyuncs.com/meiyuannet/wish_web/defaultpic.png':$user->pic) ?>" alt="图片"/>
    </div>
    <div class="invite-name">
        <p>Hi，我是<?=$user->nick_name ?><br/>
        邀请您使用美愿</p>
    </div>
</header>
<hr class="seprate">
    </div>
<div class="my-bg">
    <div class="bottom-contain">
    <div class="content">
        <p>我已经准备好故事和薄酒，<br/>独缺你来品。</p>
    </div>
    <div class="my-rule">
        <p>美愿规则：</p>
    </div>
    <div class="rule-contain">
        <div class="rule-inline rule-inline-left"><p>1、</p></div><div class="rule-inline rule-inline-right"><p>打赏我的愿望您可以获得3倍奖金，同时为愿望发起人赢得1倍奖金；</p></div>
    </div>
    <div class="rule-contain">
    <div class="rule-inline rule-inline-left"><p>2、</p></div><div class="rule-inline rule-inline-right"><p>奖金可以提现到银行卡。好友奖金最高10元，愿望发起人奖金最高5元。</p></div>
</div>
    </div>
</div>
<footer class="footer">
    <div class="footer-left">
        <p>
            <img class="imgdownload" src="http://oss.aliyuncs.com/meiyuannet/wish_web/icon.png" alt="下载">
        </p>
    </div>
    <div class="footer-right">
        <p>
            <img class="imgdownload" src="http://image.matewish.cn/system/download.png" alt="下载">
        </p>
    </div>
</footer>
<?php
$js='
$(function(){
ytop = $(".my-bg").offset().top;
ydown = $(".footer").offset().top;
h = (ydown - ytop + $(".footer").height());
if(h < 800)
{
    h = 800;
}
$(".my-bg").css({"height":h});
setTimeout(initFun,300);
$(".footer").on("click",function(){
location.href="http://www.matewish.cn/";
});
});
function initFun()
{
    $(".wrap").css({"margin-bottom":$(".imgdownload").height() + 20});
}
';
$this->registerJs($js,\yii\web\View::POS_END);
?>