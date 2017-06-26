<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/4
 * Time: 14:22
 */
?>
<style>
    body
    {
        background-color: #fc8c96;
    }
    .header
    {
        position: fixed;
        background-color: #fff;
        width: 100%;
        height: 54px;
        border: 1px solid #ebebeb;
        z-index: 99;
        top: 0;
    }
    .header-logo
    {
        margin: 12px 0px 0px 30px;
        float: left;
    }
    .header-img
    {
        width: 150px;
    }
    .code
    {
        text-align: center;
        width: 270px;
        margin: 0 auto;
        margin-top: 100px;
    }
    #code-scan
    {
        border-radius: 5px;
        width: 230px;
    }
    .footer-txt
    {
        width: 60%;
        margin-top: 30px;
        margin-bottom: 30px;
    }
    .footer
    {
        text-align: center;
    }
</style>
<div class="header">
    <div class="header-logo">
        <img class="header-img" src="http://image.matewish.cn/wish_web/icon1.png" alt="美愿logo">
    </div>
</div>
<div class="bg">
    <div class="code">
        <img src="<?= $back_url ?>" id="code-scan">
    </div>
</div>
<div class="footer">
    <img src="http://image.matewish.cn/frontmywish/weixin-bg.png" class="footer-txt">
</div>

