<?php

namespace backend\controllers\VersionManageActions;


use backend\components\ExitUtil;
use common\models\MultiUpdateContent;
use frontend\business\MultiUpdateContentUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * 新增子版本控制
 * Class CreateAction
 * @package backend\controllers\VersionManageActions
 */
class CreateSonAction extends Action
{
    public function run()
    {
        $app_id = \Yii::$app->request->post('app_id');
        if(empty($app_id)){
            $app_id = \Yii::$app->request->get('app_id');
        }
        $model = new MultiUpdateContent();
        $this->controller->getView()->title = '新增子版本控制';
        $model->status = 1;
        $model->app_id = $app_id;
        $model->is_register = 0;

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            \Yii::$app->cache->delete('app_version_info');
            return $this->controller->redirect(['indexson','app_id'=>$app_id]);
        }

        return $this->controller->render('createson', [
            'model' => $model,
        ]);

    }
}