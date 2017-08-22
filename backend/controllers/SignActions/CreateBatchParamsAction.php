<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/22
 * Time: 下午12:14
 */

namespace backend\controllers\SignActions;


use common\models\SignParams;
use yii\base\Action;

class CreateBatchParamsAction extends  Action
{
    public function run()
    {
        $model = new SignParams();
        $model->day_id = 1;
        $model->type = 1;
        if($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->controller->redirect('batchindex');
        } else{
            return $this->controller->render('_batchform',[
                'model' => $model
            ]);
        }
    }
}