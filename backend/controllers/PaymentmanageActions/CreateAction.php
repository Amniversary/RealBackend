<?php

namespace backend\controllers\PaymentmanageActions;


use common\models\Payment;
use yii\base\Action;
/**
 * 新增票转豆商品
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new Payment();
        $this->controller->getView()->title = '新增支付方式';
        $model->status = 1;
        $model->icon = 'http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/balance_pay.png';
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