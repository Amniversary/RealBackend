<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RedPackets */

//$this->title = 'Create Red Packets';
$this->params['breadcrumbs'][] = ['label' => '红包管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="red-packets-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
