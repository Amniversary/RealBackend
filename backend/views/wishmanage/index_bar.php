<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WishSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wishes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wish-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Wish', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'wish_id',
            'wish_name',
            'discribtion:ntext',
            'wish_type_id',
            'wish_type',
            // 'pic1',
            // 'pic2',
            // 'pic3',
            // 'pic4',
            // 'pic5',
            // 'pic6',
            // 'to_msg_user_id',
            // 'end_date',
            // 'create_time',
            // 'back_type',
            // 'back_dis:ntext',
            // 'min_reward',
            // 'reward_num',
            // 'wish_money',
            // 'ready_reward_money',
            // 'collect_num',
            // 'view_num',
            // 'comment_num',
            // 'province',
            // 'city',
            // 'area',
            // 'address',
            // 'longitude',
            // 'latitude',
            // 'finish_status',
            // 'finish_time',
            // 'publish_user_id',
            // 'publish_user_name',
            // 'status',
            // 'hot_num',
            // 'publish_user_phone',
            // 'is_finish',
            // 'to_balance',
            // 'back_status',
            // 'back_count',
            // 'back_money',
            // 'remar1',
            // 'remar2',
            // 'remar3',
            // 'remar4',
            // 'red_packets_money',
            // 'is_official',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
