<?php
namespace backend\controllers\ActivityPrizeActions;

use backend\models\ActivityStatisticSearch;
use yii\base\Action;
use yii;


class StatisticAction extends Action
{
	public function run()
    {
		$searchModel = new ActivityStatisticSearch();
		$dataProvider = $searchModel->search(yii::$app->request->queryParams);
		return $this->controller->render('statistic',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
		    ]
        );
	}	
}

