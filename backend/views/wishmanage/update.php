<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Wish */

$this->title = '修改愿望';
/*$this->params['breadcrumbs'][] = ['label' => 'Wishes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->wish_id, 'url' => ['view', 'id' => $model->wish_id]];
$this->params['breadcrumbs'][] = 'Update';*/
?>
<div class="wish-update">
    <?= $this->render('_form', [
        'model' => $wish,
        'type_items'=>$type_items,
    ]) ?>

</div>
<?php
$js='
$("#wish-wish_type_id").change(function(){
    text = $("#wish-wish_type_id").find("option:selected").text();
    $("#wish-wish_type").val(text);
})';
$this->registerJs($js,\yii\web\View::POS_END);
