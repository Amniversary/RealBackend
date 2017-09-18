<style>
    .user-form{
        border-radius: 5px;
        position: relative;
        border: 1px solid #d9dadc;
        background-color: #fff;
        padding: 20px;
        /*margin-bottom: 20px;*/
    }
    #compareform-compare_one{

    }
    #left{
        float: left;
        margin-right: 25px;
    }
    .col {
        display: inline-block;
        border: 1px solid #cee2ee;
        width: 17%;
        text-align: center;
        /*float: left;*/
    }
    .bg-color{
        background-color: #CEE2EE;
    }
</style>

<?php
use yii\widgets\ActiveForm;
use \yii\helpers\Html;
/**
 * @var $model \backend\models\CompareForm
 * @var $auth
 */
common\assets\ArtDialogAsset::register($this);
?>


<div class="user-form">
    <?php $form = ActiveForm::begin() ?>
        <div id="left">
            <?= $form->field($model, 'compare_one')->dropDownList($auth, ['style'=> 'width:200px']) ?>
        </div>

        <div style="margin-left: 10px">
            <?= $form->field($model, 'compare_two')->dropDownList($auth, ['style'=> 'width:200px']) ?>
        </div>
        <hr>

        <div class="form-group">
            <?= Html::button('比对' ,['class' => 'btn btn-primary', 'id' => 'compare']) ?>
        </div>
    <div id="data" style="display: none">
        <hr>
        <div id="info">

        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php
$js = '
    artDialog.tips = function(content, time) {
       return artDialog({
            id:"Tips",
            title:false,
            fixed:true,
            lock:true
       })
       .content("<div style=\"padding: 0 1em;\">" + content + "</div>")
       .time(time || 1);
}

$(document).on("click", "#compare", function(){
    var dialog = art.dialog({
            title: "测试消息发送中 ...",
            fixed:true,
            lock:true,
    });
    $.ajax({
       type:"POST",
       url:"/publiclist/compare_info",
       data:$("#w0").serialize(),
       success: function(data) {
           data = $.parseJSON(data);
           if(data.code == 0){
                artDialog.tips("数据比对完成!");
                console.log(data.data);
                $("#data").show();
                $("#info").append("<div class=\"col\"><div class=\"bg-color\">公众号1总数</div><div>" + data.data.count_one + "</div></div>");
                $("#info").append("<div class=\"col\"><div class=\"bg-color\">公众号2总数</div><div>" + data.data.count_two + "</div></div>");
                $("#info").append("<div class=\"col\"><div class=\"bg-color\">总数</div><div>" + data.data.max + "</div></div>");
                $("#info").append("<div class=\"col\"><div class=\"bg-color\">去除重复用户总数</div><div>" + data.data.count_json + "</div></div>");
                $("#info").append("<div class=\"col\"><div class=\"bg-color\">重复数</div><div>" + data.data.poor + "</div></div>");
           }else{
                art.dialog.alert("比对失败：" + data.msg);
           }
           if(dialog != null)
             dialog.close();
       },
       error:function(XMLHttpRequest, textStatus, errorThrown) {
            artDialog.tips("服务器繁忙，请稍后再试，状态：" + XMLHttpRequest.status);
            if(dialog != null) dialog.close();
        }
   })
});
';
$this->registerJs($js,\yii\web\View::POS_END);
