<?php

namespace backend\controllers\QiniuLivingActions;


use common\models\LivingParameters;
use yii\base\Action;
/**
 * 新增参数
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new LivingParameters();
        $this->controller->getView()->title = '新增参数';
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['living_parameters']);
        }
        else
        {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }
}