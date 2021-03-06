<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, minimal-ui" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection"content="telephone=no, email=no" />
    <title>直播登录</title>
    <link rel="stylesheet" href="/mibo/wx/style.css">
    <!--  <link rel="stylesheet" href="weixin/style.css"> -->
    <style>
        body,input,select,textarea {
            font-family: '微软雅黑',Arial,Helvetica,sans-serif;
            font-size: 14px
        }

        body,dd,dl,fieldset,form,h1,h2,h3,h4,h5,h6,input,legend,ol,p,select,td,textarea,th,ul {
            margin: 0;
            padding: 0
        }

        body {
            margin: 0 auto;
            background-color: #fff;
            color: #333;
            background-color: #e6e6e6;
            color: #505050;
        }

        input,select,textarea {
            outline: 0;
            -webkit-appearance: none
        }

        @media all and (min-width: 750px) {
            body {
                width:750px;
                margin-right: auto;
                margin-left: auto
            }
        }
        .p-recharge .form {
            width: 91%;
            margin: 0 auto
        }

        .p-recharge .ipt-box {
            position: relative
        }

        .p-recharge .ipt-box .tip {
            position: absolute;
            left: 4%;
            top: 9px;
            height: 22px;
            line-height: 22px;
            font-size: 14px;
            background-color: #e6e6e6;
            opacity: 0;
            display: none;
            color: #ffbe00
        }

        .p-recharge .ipt-box .ipt {
            display: block;
            width: 92%;
            margin: 8% auto 10.5%;
            line-height: 22px;
            padding: 9px 4%;
            border: 1px solid #cbcbcb;
            border-radius: 7px;
            background-color: #e6e6e6;
            color: #505050;
        }

        .p-recharge .ipt-box .ipt::-webkit-input-placeholder {
            color: #c7c7c7
        }

        .p-recharge .ipt-box .ipt:focus {
            border: 1px solid #ffbe00
        }

        .p-recharge .ipt-box .ipt:focus+.tip {
            -webkit-animation: focus .24s linear;
            animation: focus .24s linear;
            top: -11px;
            opacity: 1;
            display: block;
            background-color: #e6e6e6;
            padding: 0 4px;
            margin-left: -4px;
        }

        @-webkit-keyframes focus {
            0% {
                top: 9px;
                opacity: 0
            }

            100% {
                top: -11px;
                opacity: 1
            }
        }
        .btn-yellow {
            display: block;
            width: 100%;
            height: 44px;
            line-height: 44px;
            background-color: #ffbe00;
            color: #fff;
            border: none;
            border-radius: 22px;
            font-size: 16px
        }
        .btn-yellow:active,.btn-yellow.press {
            background-color: #ffdf80;
        }

        .btn-yellow[disabled] {
            background-color: #aaa
        }
        .withdraw-money ul li .number span.yellow{
            color:#ffbe00;
        }
        .withdraw-money{
            margin-top:20px;
            position: relative;
            width: 100%;
            font-size: 16px;
        }
        .withdraw-money .open{
            display: inline-block;
            margin-left: 10%;
            width: 30%;
            float: left;
            padding: 5px 0;
            background-color: #ffbe00;
            text-align: center;
            border-radius:  20px;
            color: #fff;
            cursor:pointer;
        }
        .withdraw-money .close{
            display: inline-block;
            width: 30%;
            margin-right: 10%;
            float: right;
            padding: 5px 0;
            background-color: #ffbe00;
            text-align: center;
            border-radius:  20px;
            color: #fff;
            cursor:pointer;
        }
        header{
            text-align: center;
        }
        header .rw-photo{
            padding-top: 4.5%;
            padding-bottom: 2.25%;
        }
        header .rw-photo img{
            width: 15.625%;
            border-radius: 50%;
        }
        header .name{
            padding-top: 2.25%;
            padding-bottom: 2%;
            font-size: 16px;
            color:#a0a0a0;
            font-weight:bold;
        }
        header .id{
            font-size: 14px;
            color:#a0a0a0;
        }

        header .balance{
            padding-top: 4.5%;
            padding-bottom: 4.5%;
            font-size: 16px;
            color:#a0a0a0;
        }
        .examine1{
            position: absolute;
            left: 50%;
            top: 40%;
            transform: translate(-50%,-50%);
            -webkit-transform: translate(-50%,-50%);
            -moz-transform: translate(-50%,-50%);
            -ms-transform: translate(-50%,-50%);
            -o-transform: translate(-50%,-50%);
        }
        .struggle{
            width: 100px;
            height: 100px;
            margin: auto;
        }
        .struggle img{
            width: 100%;
        }
        .examine1 .examine{
            padding-top: 20px;
            font-size: 16px;
        }
        .modify{
            position: absolute;
            left: 50%;
            top: 90%;
            transform: translate(-50%,-50%);
            -webkit-transform: translate(-50%,-50%);
            -moz-transform: translate(-50%,-50%);
            -ms-transform: translate(-50%,-50%);
            -o-transform: translate(-50%,-50%);
            font-size: 16px;
            padding: 5px 10px;
            background-color: #ffbe00;
            text-align: center;
            border-radius:  20px;
            color: #fff;
            cursor:pointer;
        }

        /*#login{
            display: none;
        }*/
        #examine3{
            display: none;
        }
        #examine1{
            display: none;
        }
       #divclass ~*{ display: none;}
    </style>
</head>
<body  class="p-recharge">
<div id="login">
    <form  method="post" action="/wxlivemanage/bind" >
        <div class="form">
            <input  id="openid" type="hidden"  name="openid" value="<?=$openid?>" >
            <input  id="unionid" type="hidden"  name="unionid" value="<?=unionid?>" >
            <div class="ipt-box">
                <input type="number" class="ipt" name="client_no" pattern="[0-9]" placeholder="请输入蜜播ID" id="j_id">
                <span class="tip">请输入蜜播ID</span>
            </div>
            <div class="ipt-box">
                <input type="text" class="ipt" name="name" placeholder="请输入姓名" id="j_id">
                <span class="tip">请输入姓名</span>
            </div>
            <input type="submit" value="确认绑定" class="btn-yellow" id="j_submit">
        </div>
    </form>
</div>
<script src="http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/js/jquery-1.9.1.min.js"></script>
<script src="/layer/layer.js"></script>

<div id="divclass"></div>
</body>
</html>

