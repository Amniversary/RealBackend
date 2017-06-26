<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\PaymentmanageActions;


use common\models\Payment;
use yii\base\Action;
use yii\log\Logger;

class AppliedIOSAction extends Action
{
    public function run($record_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($record_id))
        {
            $rst['msg']='id不能为空';
            echo json_encode($rst);
            exit;
        }
        $payment = Payment::findOne(['payment_id'=>$record_id]);
        if(!isset($payment))
        {
            $rst['msg']='商品不存在';
            echo json_encode($rst);
            exit;
        }
        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit))
        {
            $rst['message'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        if(empty($hasEdit))
        {
            $rst['message'] = '';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex))
        {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $modifyData = \Yii::$app->request->post('Payment');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有支付方式模型对应的数据';
            echo json_encode($rst);
            exit;
        }

        if(!isset($modifyData[$editIndex]))
        {
            $rst['message'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }
        $dataItem = $modifyData[$editIndex];
        if(!isset($dataItem['applied_ios']))
        {
            $rst['message'] = 'IOS是否显示状态值为空';
            echo json_encode($rst);
            exit;
        }
        $status = $dataItem['applied_ios'];
        $payment->applied_ios = $status;
        if(!$payment->save())
        {
            \Yii::getLogger()->log('保存IOS是否显示状态失败:'.var_export($payment->getErrors(),true),Logger::LEVEL_ERROR);
            $rst['message'] = '保存IOS是否显示状态失败';
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
}