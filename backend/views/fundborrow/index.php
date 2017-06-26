<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FundBorrowSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Borrow Funds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="borrow-fund-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Borrow Fund', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'borrow_fund_id',
            'user_id',
            'borrow_money',
            'stage_money',
            'by_stages_count',
            // 'is_back',
            // 'back_time',
            // 'borrow_rate',
            // 'breach_rate',
            // 'breach_last_rate',
            // 'half_delay_times',
            // 'create_time',
            // 'status_result',
            // 'refused_reason',
            // 'finance_has_paid',
            // 'finance_remark',
            // 'user_name',
            // 'card_no',
            // 'identity_no',
            // 'borrow_type',
            // 'reward_id',
            // 'bank_name',
            // 'remark1',
            // 'remark2',
            // 'remark3',
            // 'remark4',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
