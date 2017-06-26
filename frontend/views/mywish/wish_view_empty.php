<?php
$js='
$(function(){
deviceWidth = $(".wishviewmain").width();
url = "'.$url.'" + deviceWidth.toString();
location.href=url;
});
';
$this->registerJs($js,\yii\web\View::POS_END);
?>