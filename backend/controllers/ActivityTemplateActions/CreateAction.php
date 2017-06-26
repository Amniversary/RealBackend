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

class CreateAction extends Action
{
	public function run()
    {
        $activity_type = require(\Yii::$app->getBasePath().'/config/TemplateConfig.php') ;
		$model = new ActivityTemplate();
		if($model->load(yii::$app->request->post())){
			$model->save();
			return $this->controller->redirect(['index']);
		}else{
			return $this->controller->render('create',
                [
				    'model' => $model,
                    'activity_type' => $activity_type
			    ]
            );
		}
	} 	
}