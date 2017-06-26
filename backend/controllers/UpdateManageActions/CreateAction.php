<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 11:19
 */

namespace backend\controllers\UpdateManageActions;


use common\models\SystemParams;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $model = new SystemParams();

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['user_device_params']);
        }
        else
        {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }
} 