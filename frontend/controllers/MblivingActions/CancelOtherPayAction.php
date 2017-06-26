<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/29
 * Time: 11:11
 */

namespace frontend\controllers\MblivingActions;


use frontend\business\OtherPayUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * 取消支付
 * Class CancelOtherPayAction
 * @package frontend\controllers\MblivingActions
 */
class CancelOtherPayAction extends Action
{
    public function run()
    {
        $rst = [
            'code'=>'1',
            'msg'=>''
        ];
        if(!isset(\Yii::$app->session['openid']))
        {
            $rst['msg'] = '系统信息丢失';
            echo json_encode($rst);
            exit;
        }
        $bill_no = \Yii::$app->request->post('bill_no');
        $goods_type = \Yii::$app->request->post('goods_type');
        //\Yii::getLogger()->log('goods_type:'.$goods_type,Logger::LEVEL_ERROR);
        if(!isset($goods_type) || empty($goods_type))
        {
            $rst['msg'] = '账单类型为空，取消第三方支付失败';
            \Yii::getLogger()->log('goods_type:'.$goods_type,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        if(!isset($bill_no) || empty($bill_no))
        {
            $rst['msg'] = '账单号为空，取消第三方支付失败';
            echo json_encode($rst);
            exit;
        }
        $passParams = [
            'bill_no'=>$bill_no
        ];
        $pay_type = '100';
        if($goods_type == 1)
        {
            $pay_target = 'recharge';
        }
        else
        {
            $pay_target = 'prestore';
        }

        if(!OtherPayUtil::CancelRewardByOtherPay($passParams,$pay_type,$pay_target,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }

        $rst['code']='0';
        echo json_encode($rst);
    }
} 