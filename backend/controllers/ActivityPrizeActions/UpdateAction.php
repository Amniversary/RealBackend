<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 14:00
 */

namespace backend\controllers\ActivityPrizeActions;

use common\models\ActivityInfo;
use common\models\ActivityPrize;
use frontend\business\ActivityChanceUtil;
use yii\base\Action;
use common\models\ActivityTemplate;
use yii;


class UpdateAction extends Action
{
	public function run($prize_id)
    {
		$model = ActivityPrize::findOne(['prize_id'=>$prize_id]);
		if($model->load(Yii::$app->request->post()))
        {
            if($model->save()){
      		    return $this->controller->redirect(['index']);
            }
		}
        ActivityChanceUtil::DeleteActivityPrizeCache($error);  //删除缓存信息
        $activity = ActivityInfo::find()->asArray()->all();
        return $this->controller->render('update',
            [
                'model' => $model,
                'activity' => $activity
            ]
        );

	}	
}