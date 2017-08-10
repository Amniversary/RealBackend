<style>
    .user-pic
    {
        width: 80px;
        height: 80px;
    }
    .backend-pic-input
    {
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
/* @var $User common\models\Client */
/* @var $form yii\widgets\ActiveForm */
common\assets\ArtDialogAsset::register($this);
?>
<div class="user-form">
    <form id="w0">
        <div class="form-group field-attentionevent-nick_name">
            <label class="control-label" for="attentionevent-nick_name">用户昵称</label>
            <select id="attentionevent-nick_name" class="form-control" disabled="disabled" style="width:200px">
                <option value="1" selected=""><?= $User->nick_name ?></option>
            </select>
            <div class="help-block"></div>
        </div>
        <hr>
        <div class="form-group field-attentionevent-msg_type">
            <label class="control-label" for="attentionevent-msg_type">消息类型</label>
            <input type="hidden" name="AttentionEvent[msg_type]" value=""><div id="attentionevent-msg_type">
                <label><input type="radio" name="AttentionEvent[msg_type]" value="0" checked=""> 文本消息</label>
                <label><input type="radio" name="AttentionEvent[msg_type]" value="1"> 图文消息</label>
                <label><input type="radio" name="AttentionEvent[msg_type]" value="2"> 图片消息</label>
                <label><input type="radio" name="AttentionEvent[msg_type]" value="3"> 语音消息</label></div>
            <div class="help-block"></div>
        </div>
        <hr>
        <div id="img-text" style="display: none;">
            <label style="color: red;font-weight: bold;">标题、内容描述、外链Url、图片Url、事件Id 消息类型为图文消息时填写！</label>
            <div class="form-group field-attentionevent-title">
                <label class="control-label" for="attentionevent-title">图文标题</label>
                <input type="text" id="attentionevent-title" class="form-control" name="AttentionEvent[title]">

                <div class="help-block"></div>
            </div>    <div class="form-group field-attentionevent-description">
                <label class="control-label" for="attentionevent-description">内容描述</label>
                <input type="text" id="attentionevent-description" class="form-control" name="AttentionEvent[description]">

                <div class="help-block"></div>
            </div>    <div class="form-group field-attentionevent-url">
                <label class="control-label" for="attentionevent-url">外链Url</label>
                <input type="text" id="attentionevent-url" class="form-control" name="AttentionEvent[url]">

                <div class="help-block"></div>
            </div>    <div class="form-group field-user-pic1 has-success">
                <label class="control-label" for="user-pic1">图片(建议图片大小不要超过2MB)</label> <a class="pic-del" href="javascript:delpic('pic')">删除</a>
                <input type="hidden" name="AttentionEvent[picurl]" id="user_pic" value="">
                <input class="backend-pic-input user-pic-file" type="file" id="pic-file-pic" targetctr="pic">
                <a target="_blank" href="#" id="a-pic" style="display: none;">
                    <img class="user-pic" src="" alt="图像">
                </a>
                <div class="help-block"></div>
            </div>
            <label style="color: red;font-weight: bold;">相同的事件ID会以一条消息列表展示！</label>
            <div class="form-group field-attentionevent-event_id">
                <label class="control-label" for="attentionevent-event_id">事件 ID</label>
                <input type="text" id="attentionevent-event_id" class="form-control" name="AttentionEvent[event_id]">

                <div class="help-block"></div>
            </div>
            <hr>
        </div>

        <div id="content">
            <label style="color: red;font-weight: bold;">1.超链接中 href 为链接Url，例：&lt; a href="http://wxmp.gatao.cn" &gt;Real后台&lt; /a&gt;<br>2.回车即代表换行</label>
            <div class="form-group field-attentionevent-content">
                <label class="control-label" for="attentionevent-content">文本内容</label>
                <textarea id="attentionevent-content" class="form-control" name="AttentionEvent[content]" style="width:100%"></textarea>

                <div class="help-block"></div>
            </div>
        </div>

        <div id="img" style="display: none;">
            <div class="form-group field-user-pic1 has-success">
                <label class="control-label" for="user-pic1">图片(建议图片大小350*550)</label> <a class="pic-del" href="javascript:deletepic('pic')">删除</a>
                <input type="hidden" name="AttentionEvent[picurl1]" id="b-user_pic" value="">
                <input class="backend-pic user-pic-file" type="file" id="pic-file-pic" targetctr="pic">
                <a target="_blank" href="#" id="b-pic" style="display: none;">
                    <img class="b-user-pic" src="" alt="图像">
                </a>
                <div class="help-block"></div>
            </div>
        </div>

        <div id="video" style="display: none;">
            <div class="form-group has-success">
                <label class="control-label" for="label-video">语音(音频文件不能大于2MB 时长小于60秒)</label><a class="video-del" href="javascript:deletevideo('video')">删除</a>
                <input type="file" id="video-file" name="file">
                <input type="hidden" name="AttentionEvent[video]" id="video-user-file" value="">
                <audio src="" controls="" id="video-files"></audio>
            </div>
        </div>

        <br/>
        <div class="form-group">
            <?= Html::button('发送',['id'=>'send-msg','class' =>'btn btn-primary']) ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['template/customer']), ['class' => 'btn btn-primary']) ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?= Html::button('添加超链接标签', ['id'=>'super-link','class' =>'btn btn-primary']) ?>
        </div>
    </form>
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
    $(document).on("click","#send-msg", function() {
         if(!confirm("确定要发送消息吗?")){
              return false;
         }
         var dialog = art.dialog({
                title: "消息发送中 ...",
                fixed:true,
                lock:true,
           })
         $.ajax({
            type:"POST",
            url:"/template/send_customer?id='.$User->client_id.'",
            data: $("#w0").serialize(),
            success: function(data) {
                   data = $.parseJSON(data);
                   if(data.code == 0){
                        artDialog.tips("消息已发送成功!");
                        location = "'.\Yii::$app->urlManager->createUrl('template/customer').'";
                   }else{
                         art.dialog.alert("发送消息失败：" + data.msg);
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
