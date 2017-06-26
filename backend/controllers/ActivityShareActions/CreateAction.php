<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28
 * Time: 16:09
 */

namespace backend\controllers\ActivityShareActions;


use common\models\ActivityShareInfo;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $model = new ActivityShareInfo();
        $model->create_time = date('Y-m-d H:i:s');
        if($model->load(\Yii::$app->request->post()) && $model->save())
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