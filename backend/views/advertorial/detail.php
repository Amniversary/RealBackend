<?php
$model = \Yii::$app->cache->get("model");

?>


<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, minimal-ui" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection"content="telephone=no, email=no" />
<script src="http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/js/jquery-2.0.3.min.js"></script>
<div style="display: none;" class="title"><?php print_r($model['advertorial_title']) ?></div>
<style>
    body {
        font-family: "微软雅黑";
        font-weight: 400;
        font-size: 15px;
        color: #391b27;
        padding: 0;
        margin: 0;
    }
    .warp {
        padding:0;
        margin: 0;
    }
    img{
        width: 100%;
        padding: 0;
        margin: 0;
        display: -webkit-box;
    }
    p{
        padding: 0;
        margin: 0;
    }
</style>



<div  class="warp">
    <div class="banner-top">
        <?php print_r($model['advertorial_content']) ?>
    </div>
</div>

<?php
$js = '
    var title = $(\'.title\').text();
    document.title = title;
';
$this->registerJs($js,\yii\web\View::POS_END);
