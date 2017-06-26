<?php
namespace backend\controllers\ActivityPrizeActions;

use backend\models\ActivityPrizeSearch;
use yii\base\Action;
use yii;


class IndexAction extends Action
{
	public function run()
    {
		$searchModel = new ActivityPrizeSearch();
		$dataProvider = $searchModel->search(yii::$app->request->queryParams);
		return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
		    ]
        );
	}	
}

