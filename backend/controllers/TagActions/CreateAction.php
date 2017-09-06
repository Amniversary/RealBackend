<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/5
 * Time: 下午4:45
 */

namespace backend\controllers\TagActions;


use common\models\SystemTag;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $model = new SystemTag();
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->controller->redirect('index');
        }else{
            return $this->controller->render('_form',[
                'model' => $model
            ]);
        }
    }
}