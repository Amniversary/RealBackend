<?php

/* @var $this yii\web\View */
\common\assets\ArtDialogAsset::register($this);
\common\assets\QueryMd5Asset::register($this);
$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>

        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p>
            </div>
            <div id="showmMesg" style="margin-left: 500px;">msg</div>
            <div class="col-lg-4" id="TTTT">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p>
            </div>
        </div>
        <img style="width:300px;height: 300px" src="<?=$img_url?>">
    </div>
</div>
<input type="hidden" id="ccc">
<a href="javascript:showArtdialog()">运行</a>&nbsp;&nbsp;&nbsp;<a href="javascript:showData()">显示返回数据</a>
<?php
$js='
function showArtdialog()
{
art.dialog.open("/site/test",{
    title:"测试",
    width: "100%",
    height: "100%",
    left: "0%",
    top: "0%",
    fixed: true,
    resize: false,
    drag: false
});
}

function showData()
{
    alert(art.dialog.data("sofjosdf") == undefined);
    console.log($.md5("'.$str.'"));
}
$(function(){
    var date = new Date;
    date.setDate(date.getDate() + 300);
    console.log(date);
});
';
$this->registerJs($js,\yii\web\View::POS_END);