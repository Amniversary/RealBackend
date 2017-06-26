<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RedPackets */

$this->title = '新增红包';
$this->params['breadcrumbs'][] = ['label' => '红包管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="red-packets-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
