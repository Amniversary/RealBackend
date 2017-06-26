<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/18
 * Time: 15:11
 */

namespace backend\controllers\PaymentmanageActions;

use common\models\Payment;
use yii\base\Action;
use yii\log\Logger;

/**
 *
 * Class SetAppIdAction
 * @package backend\controllers\PaymentmanageActions
 */
class SetAppIdAction extends Action
{
    public function run($paymentid)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($paymentid))
        {
            $rst['msg']='支付id不能为空';
            echo json_encode($rst);
            exit;
        }
        $payment = Payment::findOne(['payment_id'=>$paymentid]);
        if(!isset($payment))
        {
            $rst['msg']='支付方式不存在';
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
        if(!isset($dataItem['app_id']))
        {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }

        if( is_array( $dataItem['app_id'] ) && isset( $dataItem['app_id'] ) ){
            $app_ids = $dataItem['app_id'];
            $payment->app_id = json_encode($app_ids);
        }else{
            $payment->app_id = "";
        }

        if(!$payment->save())
        {
            \Yii::getLogger()->log('保存支付方式应用在app失败:'.var_export($payment->getErrors(),true),Logger::LEVEL_ERROR);
            $rst['message'] = '保存支付方式应用在app失败';
            echo json_encode($rst);
            exit;
        }

        // 刷新缓存
        \frontend\business\PaymentsUtil::getPaymentsInCache(true);

        echo '0';
    }
}