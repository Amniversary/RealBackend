<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\PaymentmanageActions;


use common\models\Payment;
use common\models\ToBeanGoods;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($record_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($record_id))
        {
            $rst['msg']='支付方式记录id丢失';
            echo json_encode($rst);
            exit;
        }
       $payment =Payment::findOne(['payment_id'=>$record_id]);
        if(!isset($payment))
        {
            $rst['msg']='支付方式不存在';
            echo json_encode($rst);
            exit;
        }

        if($payment->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($payment->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        return $this->controller->redirect('/paymentmanage/index');
    }
}