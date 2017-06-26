<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\HotWordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hot Words';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hot-words-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Hot Words', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'hot_words_id',
            'words_type',
            'content',
            'order_no',
            'status',
            // 'remark1',
            // 'remark2',
            // 'remark3',
            // 'remark4',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
