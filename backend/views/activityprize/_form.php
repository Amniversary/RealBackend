<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

if(is_null($model->activity_id)){
	$this->title = '新增奖品信息';
}else{
	$this->title = '编辑奖品信息';
}

$dropDownList = array();
foreach ($activity as $v) {
    $dropDownList[$v['activity_id']] = $v['title'];
}
?>

<style>
	.content-header{
		height: 46px;
	}
</style>


        <div class="user-form">
			<?php 
				$form = ActiveForm::begin(['id' => 'login-form','class' => 'form']); 
			?>
                <div class="form-group field-user-pic1 <?=($model->getFirstError('pic') === null?'has-success':'has-error')?>">
                    <label class="control-label" for="user-pic">奖品图片</label> <a class="pic-del" href="javascript:delpic('pic')">删除</a>
                    <input type="hidden" name="ActivityPrize[pic]" id="user_pic" value="<?=$model->pic?>"/>
                    <input class="backend-pic-input" type="file" class="user-pic-file" id="pic-file-pic"  targetctr="pic">
                    <a target="_blank" href="#" id="a-pic" style="<?=empty($model->pic)?'display: none;':''?>">
                        <img class="user-pic" src="<?=$model->pic?>" alt="图像">
                    </a>
                    <div class="help-block"><?=$model->getFirstError('pic')?></div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'grade')->dropDownList(['1'=>'一等奖','2'=>'二等奖','3'=>'三等奖','4'=>'四等奖','5'=>'五等奖','6'=>'六等奖','7'=>'七等奖','8'=>'八等奖']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'gift_name')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'number')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'unit')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'rate')->textInput()->label('概率（%）') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'total_number')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'last_number')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'type')->dropDownList(['1'=>'鲜花','2'=>'经验值','3'=>'礼品包','4'=>'实物'])?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'activity_id')->dropDownList($dropDownList); ?>
                    </div>
                </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'order_no')->textInput()->label('排序号'); ?>
                </div>
            </div>
                </div>
				<div class="box-footer">
					<?php if(is_null($model->activity_id)){ ?>
						<?= Html::submitButton('新增',['class' => 'btn btn-primary']) ?>
					<?php }else{ ?>
						<?= Html::submitButton('编辑',['class' => 'btn btn-primary']) ?>
					<?php } ?>
					
					<a href="index" class="btn btn-primary">取消</a>
				</div>

			<?php ActiveForm::end();
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


?>

