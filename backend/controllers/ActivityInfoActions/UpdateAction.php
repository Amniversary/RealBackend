<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 14:00
 */

namespace backend\controllers\ActivityInfoActions;

use common\models\ActivityInfo;
use yii\base\Action;
use common\models\ActivityTemplate;
use yii;


class UpdateAction extends Action
{
	public function run()
    {
        $activity_type = require(\Yii::$app->getBasePath().'/config/ActivityTypeConfig.php') ;
		$activity_id = yii::$app->request->get('id');
		$model = ActivityInfo::findOne(['activity_id'=>$activity_id]);
		
		if($model->load(Yii::$app->request->post()))
        {
            if($model->save()){
      		    return $this->controller->redirect(['index']);
            }
		}
        $templates = ActivityTemplate::find()->asArray()->all();
        return $this->controller->render('update',
            [
                'model' => $model,
                'templates' => $templates,
                'activity_type' => $activity_type
            ]
        );

	}	
}