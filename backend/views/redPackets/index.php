<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\RedPacketsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Red Packets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="red-packets-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Red Packets', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'red_packets_id',
            'packets_name',
            'discribtion:ntext',
            'pic',
            'packets_money',
            // 'get_type',
            // 'overtime_days:datetime',
            // 'start_time',
            // 'end_time',
            // 'open_type',
            // 'packets_type',
            // 'remark1',
            // 'remark2',
            // 'remark3',
            // 'remark4',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
