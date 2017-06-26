<?php

namespace backend\controllers\VersionManageActions;


use frontend\business\MultiUpdateContentUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * 修改版本控制
 * Class CreateAction
 * @package backend\controllers\VersionManageActions
 */
class UpdateSonAction extends Action
{
    public function run($update_id)
    {
        $model = MultiUpdateContentUtil::GetUpdateContentById($update_id);

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            \Yii::$app->cache->delete('app_version_info');
            return $this->controller->redirect(['indexson','app_id'=>$model->app_id]);
        }

        return $this->controller->render('updateson', [
            'model' => $model,
        ]);

    }
}