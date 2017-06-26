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

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'title')->label('标题')->textInput() ?>
    <?= $form->field($model, 'start_time')->label('开始时间')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'zh-CN',
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class'=>'form-control']
    ]) ?>
    <?= $form->field($model, 'end_time',['labelOptions'=>['class'=>'control-label']])->label('结束时间')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'zh-CN',
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class'=>'form-control']
    ]) ?>
    <?= $form->field($model, 'activity_status')->label('活动状态')->dropDownList([
        '2' => '未开始',
        '1' => '进行中',
        '0' => '已结束'
    ]);  ?>
    <?= $form->field($model, 'template_id')->label('排行榜模板')->dropDownList(\backend\business\ScoreGiftUtil::GetActivityTemplate()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['scoregift/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
$js = '
//    //获得结束时间的日期
//    var end_time = $("#activitygiftscore-end_time").val();
//    //获取当天日期
//    var d = new Date()
//    var vYear = d.getFullYear()
//    var vMon = d.getMonth() + 1
//    //结束时要将天数提前一天，故将得到的天数减1
//    var vDay = d.getDate()-1
//    if(vMon < 10)
//    {
//        vMon = "0"+vMon;
//    }
//    var date = vYear+"-"+vMon+"-"+vDay;
//    $("#activitygiftscore-activity_status").change(function()
//    {
//        if($(this).val() == 0)
//        {
//            $("#activitygiftscore-end_time").val(date);
//        }else{
//            if(end_time != "")
//            {
//                $("#activitygiftscore-end_time").val(end_time);
//            }
//        };
//    })
';
$this->registerJs($js,\yii\web\View::POS_END);