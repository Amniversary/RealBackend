<style>
    .user-pic{
        width:40%;
        height:40%;
    }
    .b-user-pic{
        width:40%;
        height:40%;
    }
    .backend-pic-input
    {
        margin-bottom: 10px;
    }
    #attentionevent-content{
        height: 200px;
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
    #attentionevent-msg_type > label{
        padding-right: 20px;
        color: #333;
    }
    .form-group.has-success label{
        color: #0a0a0a;
    }
    input[type="radio"]{
        margin-right: 3px;
    }
</style>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AttentionEvent */
/* @var $form yii\widgets\ActiveForm */
/* @var $cache */
$data = [$cache['record_id']=>$cache['nick_name']];
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model,'app_id')->dropDownList($data,['disabled'=>'disabled','style'=>'width:200px']) ?>
    <hr/>
    <?= $form->field($model,'msg_type')->radioList(['0'=>'文本消息','1'=>'图文消息','2'=>'图片消息','3'=>'语音消息']) ?>
    <hr/>
    <div id="img-text">
    <label style="color: red;font-weight: bold;">标题、内容描述、外链Url、图片Url、事件Id 消息类型为图文消息时填写！</label>
    <?= $form->field($model, 'title')->textInput() ?>
    <?= $form->field($model, 'description')->textInput() ?>
    <?= $form->field($model, 'url')->textInput() ?>
    <div class="form-group field-user-pic1 <?=($model->getFirstError('picurl') === null?'has-success':'has-error')?>">
        <label class="control-label" for="user-pic1">图片(建议图片大小350*550)</label> <a class="pic-del" href="javascript:delpic('pic')">删除</a>
        <input type="hidden" name="AttentionEvent[picurl]" id="user_pic" value="<?=$model->picurl?>"/>
        <input class="backend-pic-input user-pic-file" type="file" id="pic-file-pic"  targetctr="pic" />
        <a target="_blank" href="#" id="a-pic" style="<?=empty($model->picurl)?'display: none;':''?>">
            <img class="user-pic" src="<?=$model->picurl ?>" alt="图像">
        </a>
        <div class="help-block"><?=$model->getFirstError('picurl')?></div>
    </div>
    <label style="color: red;font-weight: bold;">相同的事件ID会以一条消息列表展示！</label>
    <?= $form->field($model, 'event_id')->textInput() ?>
    <hr/>
    </div>
    <div id="content">
    <label style="color: red;font-weight: bold;">1.超链接中 href 为链接Url，例：< a href="http://wxmp.gatao.cn" >Real后台< /a><br/>
    2.回车即代表换行</label>
    <?= $form->field($model, 'content')->textarea() ?>
    </div>
    <div id="img">
        <div class="form-group field-user-pic1 <?=($model->getFirstError('picurl') === null?'has-success':'has-error')?>">
            <label class="control-label" for="user-pic1">图片(建议图片大小350*550)</label> <a class="pic-del" href="javascript:deletepic('pic')">删除</a>
            <input type="hidden" name="AttentionEvent[picurl1]" id="b-user_pic" value="<?=$model->picurl  ?>"/>
            <input class="backend-pic user-pic-file" type="file" id="pic-file-pic"  targetctr="pic" />
            <a target="_blank" href="#" id="b-pic" style="<?=empty($model->picurl)?'display: none;':''?>">
                <img class="b-user-pic" src="<?=$model->picurl ?>" alt="图像">
            </a>
            <div class="help-block"><?=$model->getFirstError('picurl')?></div>
        </div>
    </div>

    <div id="video">
        <div class="form-group <?= ($model->getFirstError('video') ===null? 'has-success':'has-error') ?>">
            <label class="control-label" for="label-video">语音(音频文件不能大于2MB 时长小于60秒)</label><a class="video-del" href="javascript:deletevideo('video')">删除</a>
            <input type="file" id="video-file" name="file">
            <input type="hidden" name="AttentionEvent[video]" id="video-user-file" value="<?=$model->video ?>"/>
            <audio src="" controls id="video-files"></audio>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['custom/customson_msg','menu_id'=>$menu_id,'parent_id'=>$parent_id]), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::button('添加超链接标签', ['id'=>'super-link','class' =>'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$js = '
$(function(){
    $vue = $("input[type=radio]:checked").val();
    if($vue == 0){
        $("#content").show();$("#img-text").hide();$("#img").hide();$("#video").hide();$("#super-link").show();
    }else if($vue == 1){
        $("#img-text").show();$("#content").hide();$("#img").hide();$("#video").hide();$("#super-link").hide();
    }else if($vue == 2){
        $("#img").show();$("#content").hide();$("#img-text").hide();$("#video").hide();$("#super-link").hide();
    }else{
        $("#video").show();$("#img-text").hide();$("#content").hide();$("#img").hide();$("#super-link").hide();
    }
});
$("input[type=\'radio\']").on("click",function(){
    $vue = $("input[type=radio]:checked").val();
    if($vue == 0){
        $("#content").show();
        $("#img-text").hide();
        $("#img").hide();
        $("#video").hide();
        $("#super-link").show();
    }else if($vue == 1){
        $("#img-text").show();
        $("#content").hide();
        $("#img").hide();
        $("#video").hide();
        $("#super-link").hide();
    }else if($vue == 2){
        $("#img").show();
        $("#content").hide();
        $("#img-text").hide();
        $("#video").hide();
        $("#super-link").hide();
    }else{
        $("#img").hide();
        $("#content").hide();
        $("#img-text").hide();
        $("#video").show();
        $("#super-link").hide();
    }
});
$("#super-link").on("click",function(){
    $text = $("#attentionevent-content").val();
    $("#attentionevent-content").val($text + "<a href=\"\"></a>");
})
function deletepic(targetKey)
{
    if(confirm("确定删除该图片吗"))
    {
        key = "b-" + targetKey;
        console.log(key);
        $("#" + key).hide();
        $("#" + key).attr("href", "");
        $("#" + key + " img").attr("src","");
        $("#b-user_" + targetKey).val("");
    }
}
function deletevideo(targetKey)
{
    if(confirm("确定删除吗"))
    {
        key = targetKey + "-files";
        $("#" + key).attr("src","");
        $("#" + key + "-user-file").val("");
    }
}
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
$(document).on("change","#video-file",function(){
        //创建FormData对象
        var data = new FormData();
        //为FormData对象添加数据
        $.each($(this)[0].files, function(i, file) {
            data.append("file", file);
            hasFile = true;
        });
        if(!hasFile)
        {
            return;
        }
        $.ajax({
            url:"/mypic/upload_video?file_type=video_type",
            type:"POST",
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success:function(data)
            {
                data = $.parseJSON(data);
                if(data.code == "0")
                {
                    $("#video-files").attr("src", data.msg);
                    $("#video-user-file").val(data.msg);
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
             }
        });
    });
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
    $(document).on("change",".backend-pic",function(){
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
                    key = "b-" + targetKey;
                    $("#" + key).show();
                    $("#" + key).attr("href", data.msg);
                    $("#" + key + " img").attr("src",data.msg);
                    $("#b-user_" + targetKey).val(data.msg);
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