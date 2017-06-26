<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 14:00
 */

namespace backend\controllers\ActivityTemplateActions;

use yii\base\Action;
use backend\models\ActivityTemplateSearch;
use yii;


class IndexAction extends Action
{
	public function run()
    {
        $activity_type = require(\Yii::$app->getBasePath().'/config/TemplateConfig.php') ;
		$searchModel = new ActivityTemplateSearch();
		$dataProvider = $searchModel->search(yii::$app->request->queryParams);
		return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'activity_type' => $activity_type
		    ]
        );
	}	
}

