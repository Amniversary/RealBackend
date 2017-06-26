<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 19:13
 */
namespace backend\controllers\DataStatisticActions;


use backend\models\LivingTimeStatisticSearch;
use yii\base\Action;

class DataLivingTimeAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '主播直播时间';
        $searchModel = new LivingTimeStatisticSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('living_time_month',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}