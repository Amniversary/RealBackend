<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 14:00
 */

namespace backend\controllers\ActivityTemplateActions;

use yii\base\Action;
use common\models\ActivityTemplate;
use yii;

class DeleteAction extends Action
{
	public function run()
    {
        $template_id = yii::$app->request->get('id');
        $model = ActivityTemplate::findOne(['template_id'=>$template_id]);
        
        if(isset($model)){
            $model -> delete();
            return $this->controller->redirect(['index']);              
        }
	}	
}