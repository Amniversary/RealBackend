<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/11
 * Time: 下午12:16
 */

namespace backend\controllers\LaterActions;


use common\models\Keywords;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '创建关键字';
        $model = new Keywords();
        $model->global = 4;
        if($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->controller->redirect('index');
        }else{
            return $this->controller->render('_form', [
                'model' => $model
            ]);
        }
    }

}