<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RedPackets */

$this->title = '红包修改';
$this->params['breadcrumbs'][] = ['label' => '红包管理', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->red_packets_id, 'url' => ['view', 'id' => $model->red_packets_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="red-packets-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
