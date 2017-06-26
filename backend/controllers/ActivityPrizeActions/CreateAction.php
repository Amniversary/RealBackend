<?php
namespace backend\controllers\ActivityPrizeActions;

use common\models\ActivityInfo;
use common\models\ActivityPrize;
use common\models\ActivityTemplate;
use frontend\business\ActivityChanceUtil;
use yii\base\Action;
use yii;

class CreateAction extends Action
{
	public function run()
    {
		$model = new ActivityPrize();
		if($model->load(yii::$app->request->post()))
        {
            if($model->save()){
                return $this->controller->redirect(['index']);
            }
            ActivityChanceUtil::DeleteActivityPrizeCache($error);  //删除缓存信息
		}
        $activity = ActivityInfo::find()->asArray()->all();
        return $this->controller->render('create',
            [
                'model' => $model,
                'activity' => $activity
            ]
        );

	} 	
}