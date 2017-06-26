<?php

namespace backend\controllers\VersionManageActions;


use common\models\MultiVersionInfo;
use yii\base\Action;

/**
 * 新增版本控制
 * Class CreateAction
 * @package backend\controllers\VersionManageActions
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new MultiVersionInfo();
        $this->controller->getView()->title = '新增版本控制';
        $model->status = 1;
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            \Yii::$app->cache->delete('app_version_info');
            return $this->controller->redirect(['index']);
        }
        return $this->controller->render('create', [
            'model' => $model,
        ]);

    }
}