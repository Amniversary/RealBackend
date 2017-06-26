<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MyBillSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bills';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bill-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Bill', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'bill_id',
            'borrow_fund_id',
            'user_id',
            'back_fee',
            'back_date',
            // 'status',
            // 'pay_bill',
            // 'other_pay_bill',
            // 'pay_type',
            // 'by_stages_count',
            // 'cur_stage',
            // 'pay_times:datetime',
            // 'create_time',
            // 'back_time',
            // 'is_cur_stage',
            // 'real_back_fee',
            // 'breach_fee',
            // 'last_breach_fee',
            // 'breach_days',
            // 'is_check_delay',
            // 'is_delay',
            // 'bad_bill_remark',
            // 'bad_mark_user_id',
            // 'bad_mark_user_name',
            // 'remark1',
            // 'remark2',
            // 'remark3',
            // 'remark4',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
