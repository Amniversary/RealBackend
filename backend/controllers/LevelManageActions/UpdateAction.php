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


class UpdateAction extends Action{
	public function run(){
		$level_id = yii::$app->request->get('id');
		$model = Level::findOne(['level_id'=>$level_id]);
		
		if($model->load(Yii::$app->request->post())){
			$model -> save();
      		return $this->controller->redirect(['index']);				
		}else{
			$levelStage = LevelStage::find()->asArray()->all();
			return $this->controller->render('update', [
				'model' => $model,
				'levelStage' => $levelStage
			]);
		}
	}	
}