<?php

namespace backend\controllers\PaymentmanageActions;


use backend\components\ExitUtil;
use common\models\Payment;
use frontend\business\ToBeanGoodsUtil;
use yii\base\Action;
/**
 * 修改支付方式
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class UpdateAction extends Action
{
    public function run($record_id)
    {
        $model = Payment::findOne(['payment_id'=>$record_id]);

        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('支付方式不存在');
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }
}