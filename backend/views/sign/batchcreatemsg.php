<style>
    .user-pic{
        width:40%;
        height:40%;
    }
    .backend-pic-input
    {
        margin-bottom: 10px;
    }
    .backend-pic{
        margin-bottom: 10px;
    }
    .user-form{
        border-radius: 5px;
        position: relative;
        border: 1px solid #d9dadc;
        background-color: #fff;
        padding: 20px;
        /*margin-bottom: 20px;*/

    }
    .form-group.has-success label{
        color: #0a0a0a;
    }
</style>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SignImage */
/* @var $form yii\widgets\ActiveForm */
/* @var $cache */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group field-user-pic1 <?=($model->getFirstError('pic_url') === null?'has-success':'has-error')?>">
        <label class="control-label" for="user-pic1">图片(建议图片大小450*800)</label> <a class="pic-del" href="javascript:delpic('pic')">删除</a>
        <input type="hidden" name="SignImage[pic_url]" id="user_pic" value="<?=$model->pic_url ?>"/>
        <input class="backend-pic-input user-pic-file" type="file" id="pic-file-pic"  targetctr="pic" />
        <a target="_blank" href="#" id="a-pic" style="<?=empty($model->pic_url)?'display: none;':''?>">
            <img class="user-pic" src="<?=$model->pic_url ?>" alt="图像">
        </a>
        <div class="help-block"><?=$model->getFirstError('pic_url')?></div>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['sign/batch_index_msg', 'id'=>$id]), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$js = '
function delpic(targetKey)
{
    if(confirm("确定删除该图片吗"))
    {
        key = "a-" + targetKey;
        console.log(key);
        sourceUrl = $("#" + key).attr("href");
        if(sourceUrl == "http://image.matewish.cn/wish_web/person-1.png")
        {
            return;
        }
        $("#" + key).hide();
        $("#" + key).attr("href", "");
        $("#" + key + " img").attr("src","");
        $("#user_" + targetKey).val("");
    }
}
$(function(){
    $(document).on("change",".backend-pic-input",function(){
        //创建FormData对象
        var data = new FormData();
        //为FormData对象添加数据
        //
        hasFile = false;
        $.each($(this)[0].files, function(i, file) {
            data.append("upload_file", file);
            hasFile = true;
        });
        if(!hasFile)
        {
            return;
        }
        var targetKey = $(this).attr("targetctr");
        $.ajax({
            url:"/mypic/upload_pic?pic_type=back_user",
            type:"POST",
            data:data,
            cache: false,
            contentType: false,    //不可缺
            processData: false,    //不可缺
            success:function(data)
            {
                file = $("#pic-file-"+ targetKey);
                file.after(file.clone().val(""));
                file.remove();
                data = $.parseJSON(data);
                if(data.code == "0")
                {
                    key = "a-" + targetKey;
                    $("#" + key).show();
                    $("#" + key).attr("href", data.msg);
                    $("#" + key + " img").attr("src",data.msg);
                    $("#user_" + targetKey).val(data.msg);
                }
                else
                {
                    alert(data.msg);
                }
                console.log(data);

            },
            error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                alert("服务器繁忙，稍后再试，状态：" + XMLHttpRequest.status);
                 file = $(this);
                 file.after(file.clone().val(""));
                 file.remove();
             }
        });
    });
});
';
$this->registerJs($js,\yii\web\View::POS_END);