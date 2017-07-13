<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/11
 * Time: 下午2:15
 */

?>

<h1>1111</h1>



<?php
$js = '
 $(document).ready(function(){
    $url = "http://www.admin.cn/realtech/login"
            $.ajax({
        type: "POST",
        url: $url,
        data: {
            "RealtechLoginSearch[username]":111,
            "RealtechLoginSearch[password]":222,
            },
        success: function(data)
            {
               data = $.parseJSON(data);
                if(data.code == "0")
                {
                    alert(data.code);
                }
                else
                {
                    alert("请求失败：" + data.msg);
                }
            },
        error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
             }
        });
        return false;
});
';
$this->registerJs($js,\yii\web\View::POS_END);
