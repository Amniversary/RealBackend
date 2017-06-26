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


class UpdateAction extends Action
{
	public function run()
    {
		$template_id = yii::$app->request->get('id');
        $activity_type = require(\Yii::$app->getBasePath().'/config/TemplateConfig.php') ;
		$model = ActivityTemplate::findOne(['template_id'=>$template_id]);
		
		if($model->load(Yii::$app->request->post())){
			$model -> save();
      		return $this->controller->redirect(['index']);				
		}else{
			return $this->controller->render('update',
                [
				    'model' => $model,
                    'activity_type' => $activity_type
			    ]
            );
		}
	}	
}