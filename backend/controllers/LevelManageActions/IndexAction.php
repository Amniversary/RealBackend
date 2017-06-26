<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/2
 * Time: 16:00
 */

namespace backend\controllers\LevelManageActions;

use yii\base\Action;
use backend\models\LevelManageSearch;
use yii;


class IndexAction extends Action{
	public function run(){
		$searchModel = new LevelManageSearch();
		$dataProvider = $searchModel->search(yii::$app->request->queryParams);

		return $this->controller->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider
		]);
	}	
}

