<style>
    .user-pic
    {
        width: 80px;
        height: 80px;
    }
    .user-form{
        border-radius: 5px;
        position: relative;
        border: 1px solid #d9dadc;
        background-color: #fff;
        padding: 20px;
    }
</style>
<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
/**
 * @var $model \common\models\BatchCustomer
 */

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'task_name')->textInput() ?>

    <div class="form-group field-batchcustomer-create_time">
        <label class="control-label" for="batchcustomer-create_time">定时发送</label>
        <?php echo \kartik\datetime\DateTimePicker::widget([
            'name' => 'BatchCustomer[create_time]',
            'options' => ['class' => 'form-control','id'=>'time-option'],
            'pluginOptions' => [
                'autoclose' => false,
                'format' => 'yyyy-mm-dd hh:ii:00',
                'todayHighlight' => true
            ]
        ]); ?>
        <div class="help-block"></div>
    </div>
    <hr>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['template/batch_customer', 'id'=>$id]), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
