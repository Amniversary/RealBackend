<?php

namespace backend\controllers\AdvertisingManageActions;


use common\models\AdImages;
use yii\base\Action;
/**
 * 新增弹窗广告图
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new AdImages();
        $model->status = 0;
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }
} 