<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/11
 * Time: 下午4:14
 */

namespace backend\controllers\LaterActions;


use common\models\LaterParams;
use yii\base\Action;

class CreateParamsAction extends Action
{
    public function run()
    {
        $model = new LaterParams();
        if($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->controller->redirect(['indexparams']);
        }else{
            return $this->controller->render('_form_params',[
                'model' => $model,
            ]);
        }
    }
}