<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 14:00
 */

namespace backend\controllers\ActivityInfoActions;

use yii\base\Action;
use common\models\ActivityInfo;
use yii;

class DeleteAction extends Action
{
	public function run()
    {
        //echo "delete";
        $activity_id = yii::$app->request->get('id');
        $model = ActivityInfo::findOne(['activity_id'=>$activity_id]);
        
        if(isset($model)){
            $model -> delete();
            return $this->controller->redirect(['index']);              
        }
	}	
}