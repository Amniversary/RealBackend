<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RedPackets */

$this->title = $model->red_packets_id;
$this->params['breadcrumbs'][] = ['label' => 'Red Packets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="red-packets-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->red_packets_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->red_packets_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'red_packets_id',
            'packets_name',
            'discribtion:ntext',
            'pic',
            'packets_money',
            'get_type',
            'overtime_days:datetime',
            'start_time',
            'end_time',
            'open_type',
            'packets_type',
            'remark1',
            'remark2',
            'remark3',
            'remark4',
        ],
    ]) ?>

</div>
