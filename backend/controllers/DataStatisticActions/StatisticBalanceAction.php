<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 19:55
 */

namespace backend\controllers\DataStatisticActions;


use backend\models\StatisticBalanceSearch;
use yii\base\Action;

class StatisticBalanceAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '平台收支统计';
        $searchModel = new StatisticBalanceSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('statistic_balance',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 