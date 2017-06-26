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
</style>
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 14:03
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
use common\models\MultiVersionInfo;

$applist =MultiVersionInfo::find()->select(['app_id','app_name'])->all();
foreach ($applist as $key=>$app ){
    $versionInfo[$app->app_id] = $app->app_name."($app->app_id)";
}

?>

    <div class="user-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'app_id')->dropDownList($versionInfo)?>

        <div class="form-group field-user-pic1 <?=($model->getFirstError('img_url') === null?'has-success':'has-error')?>">
            <label class="control-label" for="user-pic1">广告图片</label> <a class="pic-del" href="javascript:delpic('pic')">删除</a>
            <input type="hidden" name="Advertise[img_url]" id="user_pic" value="<?=$model->img_url?>"/>
            <input class="backend-pic-input" type="file" class="user-pic-file" id="pic-file-pic"  targetctr="pic">
            <a target="_blank" href="#" id="a-pic" style="<?=empty($model->img_url)?'display: none;':''?>">
                <img class="user-pic" src="<?=$model->img_url?>" alt="图像">
            </a>
            <div class="help-block"><?=$model->getFirstError('pic')?></div>
        </div>

        <?= $form->field($model, 'width')->textInput() ?>

        <?= $form->field($model, 'height')->textInput() ?>

        <?= $form->field($model, 'target_url')->textInput() ?>

        <?= $form->field($model, 'duration')->textInput() ?>

        <?= $form->field($model, 'effe_time',['labelOptions'=>['class'=>'control-label']])->label('起始时间')->widget(\yii\jui\DatePicker::classname(), [
            'language' => 'zh-CN',
            'dateFormat' => 'yyyy-MM-dd',
            'options' => ['class'=>'form-control']
        ]) ?>

        <?= $form->field($model, 'end_time',['labelOptions'=>['class'=>'control-label']])->label('结束时间')->widget(\yii\jui\DatePicker::classname(), [
            'language' => 'zh-CN',
            'dateFormat' => 'yyyy-MM-dd',
            'options' => ['class'=>'form-control']
        ]) ?>

        <?= $form->field($model, 'ordering')->textInput() ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['advertise/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
        sourceUrl = $("#" + key).attr("href");
        if(sourceUrl == "http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/person-1.png")
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
