<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/2
 * Time: 16:00
 */

namespace backend\controllers\LevelManageActions;

use yii\base\Action;
use common\models\Level;
use common\models\LevelStage;
use yii;

class CreateAction extends Action{
	public function run(){
		$model = new Level();
		if($model->load(yii::$app->request->post())){
			$model->save();
			return $this->controller->redirect(['index']);
		}else{
			$levelStage = LevelStage::find()->asArray()->all();
			//var_dump($levelStage);
			return $this->controller->render('create', [
				'model' => $model,
				'levelStage' => $levelStage
			]);
		}		
	} 	
}