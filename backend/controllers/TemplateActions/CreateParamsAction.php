<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/7
 * Time: 下午2:49
 */

namespace backend\controllers\TemplateActions;


use common\models\BatchCustomer;
use yii\base\Action;

class CreateParamsAction extends Action
{
    public function run()
    {
        $model = new BatchCustomer();
        $model->status = 1;
        $post = \Yii::$app->request->post();
        if(!empty($post['BatchCustomer']['create_time'])) {
            $post['BatchCustomer']['create_time'] = strtotime($post['BatchCustomer']['create_time']);
        }
        if ($model->load($post) && $model->save()) {
            return $this->controller->redirect('batch_customer');
        } else {
            return $this->controller->render('create_params', [
                'model' => $model
            ]);
        }
    }
}