<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RedPackets */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="red-packets-form" style="margin-top: 30px;">

    <?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]); ?>

    <?= $form->field($model, 'packets_name')->textInput(['maxlength' => true,'style'=>'display:inline-block;width:200px;']) ?>

    <?= $form->field($model, 'pic')->fileInput(['maxlength' => true,'style'=>'display:inline-block;width:200px;']) ?>

    <?= $form->field($model, 'over_pic')->fileInput(['maxlength' => true,'style'=>'display:inline-block;width:200px;']) ?>
    
    <?= $form->field($model, 'packets_money')->textInput(['maxlength' => true,'style'=>'display:inline-block;width:200px;']) ?>

    <?= $form->field($model, 'packets_type')->dropDownList(['64'=>'打赏奖励红包','256'=>'打赏愿望奖励红包','260'=>'签到红包'],['style'=>'display:inline-block;width:200px;']) ?>

    <?= $form->field($model, 'get_type')->dropDownList(['1'=>'领取后N天过期','2'=>'设置过期日期'],['style'=>'display:inline-block;width:200px;']) ?>

    <?= $form->field($model, 'overtime_days',['options'=>['style'=>($model->get_type == '1'?'':'display:none')]])->textInput(['value'=>'7','style'=>'display:inline-block;width:200px;']) ?>

    <?= $form->field($model, 'start_time',['options'=>['style'=>($model->get_type == '2'?'':'display:none')]])->widget(\yii\jui\DatePicker::className(),[
        'model'=>$model,
        'language'=>'zh-CN',
        'dateFormat'=>'yyyy-MM-dd',
        //'attribute'=>'start_time',
        'options'=>['class'=>'form-control','readonly'=>true,'style'=>'display:inline-block;width:200px;']
    ]); ?>

    <?= $form->field($model, 'end_time',['options'=>['style'=>($model->get_type == '2'?'':'display:none')]])->widget(\yii\jui\DatePicker::className(),[
        'model'=>$model,
        'language'=>'zh-CN',
        'dateFormat'=>'yyyy-MM-dd',
        'options'=>['class'=>'form-control','readonly'=>true,'style'=>'display:inline-block;width:200px;']
    ]); ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['redpacket/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end();?>

</div>
<?php
    $js = '
    $("#redpackets-get_type").change(function(){
        getType = $(this).val();
        if(getType == "1")
        {
            $(".field-redpackets-overtime_days").show();
            $(".field-redpackets-start_time").hide();
            $(".field-redpackets-end_time").hide();
        }
        else
        {
            $(".field-redpackets-overtime_days").hide();
            $(".field-redpackets-start_time").show();
            $(".field-redpackets-end_time").show();
        }
    });

    ';
$this->registerJs($js,\yii\web\View::POS_END);


