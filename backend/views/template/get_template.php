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
    #temp{
        background-color: #EBEBEB;
        /*height: 150px;*/
        padding: 10px 0 10px 10px;
    }
    #template-content{
        border:solid 1px #D8D8D8;
        width: 400px;
        border-radius: 3px;
        background-color: #FFF;
        padding: 5px 0 5px 5px;
    }
    .template-label{
        margin-bottom: 0px;
    }
    #Template-url{
        width: 40%;
    }
    #Template-openid{
        width: 40%;
    }
    .test-send{

    }
</style>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $template common\models\Template */
/* @var $form yii\widgets\ActiveForm */
/* @var $cache */
common\assets\ArtDialogAsset::register($this);
?>
<div class="user-form">
    <form id="w0">
    <div class="form-group field-template-app_id">
        <label class="control-label" for="template-title">模板标题</label>
        <select id="template-app_id" class="form-control" name="Template[title]" disabled="disabled" style="width:200px">
            <option value="1" selected=""><?= $template->title ?></option>
        </select>
        <div class="help-block"></div>
    </div>
    <hr>
    <div id="temp">
        <div id="template-content">
            <label class="template-label"><?= $template->title ?></label><br />
            <p style="color: #8E8E8E;;"><?= date('Y-m-d') ?></p>
            <?php foreach($data as $item){ ?>
                <label class="template-label"><?= strpos($item['text'],'DATA') ? '': $item['text'].':' ?></label>
                <label class="template-label" id="<?= $item['format'].'-label' ?>"></label>&nbsp;
                <a href="#" id="<?= $item['format'].'-a' ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                <input type="hidden" name="Template[<?= $item['format'] ?>][value]" id="Template-<?= $item['format'] ?>-hide" value=""/>
                <input type="hidden" name="Template[<?= $item['format'] ?>][color]" id="Template-<?= $item['format'] ?>-color-hide" value=""/>
                <br/>
            <?php } ?>
        </div>
    </div>
    <hr>
    <div class="form-group field-Template-time">
        <label class="control-label" for="template-time">定时发送</label>
        <?php echo \kartik\datetime\DateTimePicker::widget([
            'name' => 'Template[time]',
            'options' => ['class' => 'form-control'],
            'pluginOptions' => [
                'autoclose' => false,
                'format' => 'yyyy-mm-dd hh:ii:00',
                'todayHighlight' => true
            ]
        ]); ?>
        <div class="help-block"></div>
    </div>
    <div class="form-group field-Template-url">
        <label class="control-label" for="template-url">跳转链接</label>
        <input type="text" id="Template-url" class="form-control" name="Template[url]">
        <div class="help-block"></div>
    </div>
    <div class="form-group field-Template-openid">
        <label class="control-label" for="template-url">测试发送（openId）</label>
        <input type="text" id="Template-openid" class="form-control" name="Template[openid]">
        <div class="help-block"></div>
    </div>
    <br/>
    <div class="form-group">
        <?= Html::button('保存',['id'=>'send-msg','class' =>'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['template/index']), ['class' => 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::button('测试发送',['class'=>'btn btn-success test-send']) ?>
    </div>
    </form>
</div>

<?php
$js = '';
foreach($data as $item) {
    $js .= '
     $(document).on("click", "#' . $item['format'] . '-a",function(){
           var dialog = art.dialog({
                content: "<textarea type=\"text\" style=\"width:500px;\" id=\"' . $item['format'] . '-text\"></textarea>"
                    + "<select id=\"template-color\" class=\"form-control\" style=\"width:100px\">"
                    + "<option value=\"#173177\" selected=\"\">蓝</option>"
                    + "<option value=\"#135EFB\" selected=\"\">天蓝</option>"
                    + "<option value=\"#FF0000\" selected=\"\">红</option>"
                    + "<option value=\"\" selected=\"\">黑</option>"
                    + "</select>"
                    + "<br/><label style=\"color:#8E8E8E\">文本中加入 {{NICKNAME}} 会替换成用户昵称</label>",
                fixed: true,
                lock:true,
                ok:function() {
                    $text = $("#' . $item['format'] . '-text").val();
                    $value = $("#template-color option:selected").val();
                    $("#'. $item['format'] .'-label").html($text);
                    $("#Template-'. $item['format'].'-hide").val($text);
                    $("#Template-'. $item['format'].'-color-hide").val($value);
                    $("#'. $item['format'] .'-label").css("color", $value);
                },
                cancel:true,
           })
           $vue = $("#Template-'. $item['format'].'-hide").val();
           $("#' . $item['format'] . '-text").val($vue);
     });
';
}
$js .= '
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

    $(document).on("click", ".test-send" ,function(){
           $openid = $("#Template-openid").val();
           if($openid == ""){
                artDialog.tips("测试帐号: openId 不能为空!");
                return false;
           }
           var dialog = art.dialog({
                title: "测试消息发送中 ...",
                fixed:true,
                lock:true,
           })
           $.ajax({
               type:"POST",
               url:"/template/send_template?t=test&id='.$template->id.'",
               data:$("#w0").serialize(),
               success: function(data) {
                   data = $.parseJSON(data);
                   if(data.code == 0){
                        artDialog.tips("测试消息发送完成!");
                   }else{
                         art.dialog.alert("测试消息失败：" + data.msg);
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
    $(document).on("click","#send-msg", function() {
         if(!confirm("确定要保存或发送消息吗?")){
              return false;
         }
         var dialog = art.dialog({
                title: "将消息加入发送队列中 ...",
                fixed:true,
                lock:true,
           })
         $.ajax({
            type:"POST",
            url:"/template/send_template?t=real&id='.$template->id.'",
            data: $("#w0").serialize(),
            success: function(data) {
                   data = $.parseJSON(data);
                   if(data.code == 0){
                        artDialog.tips("消息已加入发送队列!");
                        location = "'.\Yii::$app->urlManager->createUrl('template/index').'";
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
';
$this->registerJs($js,\yii\web\View::POS_END);
