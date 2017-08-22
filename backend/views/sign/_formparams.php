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
</style>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SignParams */
/* @var $form yii\widgets\ActiveForm */
$data = [$cache['record_id']=>$cache['nick_name']];
?>

    <div class="user-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model,'app_id')->dropDownList($data,['disabled'=>'disabled','style'=>'width:200px']) ?>
        <hr>
        <?= $form->field($model, 'day_id')->dropDownList(\backend\business\SignParamsUtil::GetSignDayParams($cache['record_id']),['style'=>'width:200px']) ?>
        <br/>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['sign/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end();?>
    </div>
<?php
$js = '
';
$this->registerJs($js,\yii\web\View::POS_END);
