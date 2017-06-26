<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 14:00
 */

namespace backend\controllers\ActivityInfoActions;

use common\models\ActivityInfo;
use common\models\ActivityTemplate;
use yii\base\Action;
use yii;

class CreateAction extends Action
{
	public function run()
    {
        $activity_type = require(\Yii::$app->getBasePath().'/config/ActivityTypeConfig.php') ;
		$model = new ActivityInfo();
		if($model->load(yii::$app->request->post()))
        {
            $model->create_time = date("Y-m-d H:i:s",time());
            if($model->save()){
                return $this->controller->redirect(['index']);
            }
		}
        $templates = ActivityTemplate::find()->asArray()->all();
        return $this->controller->render('create',
            [
                'model' => $model,
                'templates' => $templates,
                'activity_type' => $activity_type
            ]
        );

	} 	
}