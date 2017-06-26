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

    .user-update
    {
        width: 1200px;
    }
</style>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>
<div class="user-form">

    <div class="table-responsive">
        <table class="table">
            <td style="font-size: 20px;width: 250px"> 蜜播ID：<?= $client_no['client_no']  ?></td>
            <tr>
                <td>排序号</td>
                <td>参数ID</td>
                <td>视频帧数</td>
                <td>编码耗能</td>
                <td>传输码率</td>
                <td>视频宽度</td>
                <td>视频高度</td>
            </tr>
            <?php foreach($model as $i => $data_v){?>

            <tr>
                <td><?= $form->field($data_v, "[$i]order_no")->label('')->textInput() ?></td>
                <td style="width: 300px;"><?= $form->field($data_v, "[$i]quality")->label('')->dropDownList(\frontend\business\ClientQiNiuUtil::GetLivingParameters()) ?></td>
                <td><?= $form->field($data_v, "[$i]fps")->label('')->textInput() ?></td>
                <td><?= $form->field($data_v, "[$i]profilelevel")->label('')->textInput() ?></td>
                <td><?= $form->field($data_v, "[$i]video_bit_rate")->label('')->textInput() ?></td>
                <td><?= $form->field($data_v, "[$i]width")->label('')->textInput() ?></td>
                <td><?= $form->field($data_v, "[$i]height")->label('')->textInput() ?></td>
                <?php if(count($model) >= 3) { ?>
                <td><button style="margin-top: 18px;" type="button" class="btn btn-danger del" onclick="deletes($(this))">删除</button></td>
                <?php } ?>
            </tr>
            <?php } ?>
        </table>
    </div>


    <div class="form-group">
        <?= Html::submitButton('修改', ['class' =>  'btn btn-primary submit']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <?= Html::a('取消',\Yii::$app->urlManager->createUrl(['qiniuliving/client_params']), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js = '

    function deletes(e){
        e.parents("tr").remove();
        if($("tr").length <= 5){
            $(".del").hide();
        };
    }

';
$this->registerJs($js,\yii\web\View::POS_END);


