<?php

namespace backend\controllers\VersionManageActions;


use frontend\business\MultiVersionInfoUtil;
use yii\base\Action;

/**
 * 修改版本控制
 * Class CreateAction
 * @package backend\controllers\VersionManageActions
 */
class UpdateAction extends Action
{
    public function run($record_id)
    {
        $model = MultiVersionInfoUtil::GetVersionById($record_id);

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            \Yii::$app->cache->delete('app_version_info');
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }
}