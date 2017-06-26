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

    <div class="form-group field-goldsaccount-gold_account_balance has-success">
        <label class="control-label" for="goldsaccount-gold_account_balance">用户ID</label>
        <input type="text" id="goldsaccount-gold_account_balance" class="form-control" readonly="true"  value="<?=$model->user_id?>">

        <div class="help-block"></div>
    </div>
    <?php $ClientModel = \common\models\GoldsAccount::GetClientInfo($model->user_id)?>
    <div class="form-group field-goldsaccount-gold_account_balance has-success">
        <label class="control-label" for="goldsaccount-gold_account_balance">蜜播ID</label>
        <input type="text" id="goldsaccount-gold_account_balance" class="form-control" readonly="true"  value="<?= $ClientModel->client_no?>">

        <div class="help-block"></div>
    </div>
    <div class="form-group field-goldsaccount-gold_account_balance has-success">
        <label class="control-label" for="goldsaccount-gold_account_balance">用户名称</label>
        <input type="text" id="goldsaccount-gold_account_balance" class="form-control" readonly="true"  value="<?=$ClientModel->nick_name?>">

        <div class="help-block"></div>
    </div>
    <div class="form-group field-goldsaccount-gold_account_balance has-success">
        <label class="control-label" for="goldsaccount-gold_account_balance">当前金币余额</label>
        <input type="text" id="goldsaccount-gold_account_balance" class="form-control" readonly="true" name="GoldsAccount[gold_account_balance]" value="<?=$model->gold_account_balance?>">

        <div class="help-block"></div>
    </div>

    <div class="form-group field-goldsaccount-gold_account_balance has-success">
        <label class="control-label" for="goldsaccount-gold_account_balance">回调金额(减)</label>
        <input type="text" id="goldsaccount-gold_account_balance" class="form-control" name="GoldsAccount[gold_account_balance_less]" >

        <div class="help-block"></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['goldsaccount/index']), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>