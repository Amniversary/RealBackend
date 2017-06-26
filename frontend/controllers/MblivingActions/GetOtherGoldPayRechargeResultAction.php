<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/7
 * Time: 17:46
 */

namespace frontend\controllers\MblivingActions;


use frontend\business\RechargeListUtil;
use yii\base\Action;
use yii\log\Logger;

class GetOtherGoldPayRechargeResultAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1', 'msg'=>''];
        //对应美愿的openid ovfNUwxU7RrMRTaatWytkFkFxiQQ
        //\Yii::$app->session['openid'] = 'ovfNUwxU7RrMRTaatWytkFkFxiQQ';
        if(!isset(\Yii::$app->session['openid']))
        {
            $rst['msg'] = '系统信息丢失';
            echo json_encode($rst);
            exit;
        }
        $open_id = \Yii::$app->session['openid'];
        $bill_no = \Yii::$app->request->post('bill_no');

        if(!isset($bill_no))
        {
            $rst['msg']='账单号参数为空';
            echo json_encode($rst);
            exit;
        }
        $recharge = RechargeListUtil::GetRechargeGoldByBillNo($bill_no);
        \Yii::getLogger()->log('recharge_goldaaaaa:'.var_export($recharge,true),Logger::LEVEL_ERROR);
        if(!isset($recharge))
        {
            $rst['msg']='金币充值记录不存在';
            \Yii::getLogger()->log($rst['msg'].' bill_no:'.$bill_no,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        if($recharge->status_result === 2)
        {
            $code = 0;
            $msg = '支付成功';
        }
        else if($recharge->status_result === 1)
        {
            $code = '2';
            $msg = '支付中';
        }
        else if($recharge->status_result === 0)
        {
            $code = '1';
            $msg = '已经取消支付';
        }
        else
        {
            $code = '1';
            $msg = '支付失败';
        }
        $rst['code'] = $code;
        $rst['msg'] = $msg;
        echo json_encode($rst);
    }
} 