<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\GetCashSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Get Cashes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="get-cash-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Get Cash', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'get_cash_id',
            'user_id',
            'nick_name',
            'cash_money',
            'cash_rate',
            // 'cash_fees',
            // 'real_cash_money',
            // 'status',
            // 'refuesd_reason',
            // 'finance_remark',
            // 'identity_no',
            // 'real_name',
            // 'card_no',
            // 'bank_name',
            // 'create_time',
            // 'check_time',
            // 'finace_ok_time',
            // 'remark1',
            // 'remark2',
            // 'remark3',
            // 'remark4',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
