
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
 * Date: 2016/12/19
 * Time: 20:03
 */


use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="user-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'client_no')->label('密播ID')->textInput() ?>


        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['advertise/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php
$js = '
';
$this->registerJs($js,\yii\web\View::POS_END);
