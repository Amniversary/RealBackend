<?php

namespace backend\controllers\UpdateManageActions;


use common\models\MultiVersionInfo;
use yii\base\Action;

/**
 * 新增版本控制
 * Class UpdateCreateAction
 * @package backend\controllers\UpdateManageActions
 */
class UpdateCreateAction extends Action
{
    public function run()
    {
        $model = new MultiVersionInfo();
        $this->controller->getView()->title = '新增版本控制';
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('update_create', [
                'model' => $model,
            ]);
        }
    }
}