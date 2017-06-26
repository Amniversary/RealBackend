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
use yii;

class DeleteAction extends Action{
	public function run(){
        $level_id = yii::$app->request->get('id');
        $model = Level::findOne(['level_id'=>$level_id]);
        
        if(isset($model)){
            $model -> delete();
            return $this->controller->redirect(['index']);              
        }
	}	
}