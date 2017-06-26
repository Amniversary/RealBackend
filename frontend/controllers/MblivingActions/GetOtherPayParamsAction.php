<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/2/29
 * Time: 10:42
 */

namespace frontend\controllers\MblivingActions;


use common\components\DeviceUtil;
use frontend\business\OtherPayUtil;
use yii\base\Action;
use yii\log\Logger;

class GetOtherPayParamsAction extends Action
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
        //\Yii::getLogger()->log('openid:'.$open_id,Logger::LEVEL_ERROR);
        $passParams['goods_id'] = \Yii::$app->request->post('goods_id'); //商品id
        $passParams['unique_no'] = \Yii::$app->request->post('unique_no'); //唯一号
        $passParams['goods_type'] = \Yii::$app->request->post('goods_type');//充值商品类型
        /*if($passParams['goods_type'] == 2)
        {
            $rst['msg'] = '金币充值功能开发中，敬请期待！';
            echo json_encode($rst);
            exit;
        }*/
        if(!isset($passParams))
        {
            $rst['msg']='参数为空';
            echo json_encode($rst);
            exit;
        }
        $passParams['open_id'] = $open_id;
        $passParams['user_id'] = \Yii::$app->session['recharge_user_id'];
        $pay_type = '100';
        if(!in_array($pay_type,['100']))
        {
            $rst['msg']='支付类型错误';
            echo json_encode($rst);
            exit;
        }
        $deviceType = DeviceUtil::GetDeviceType();
        $passParams['device_type'] = $deviceType;
        $pay_target = 'recharge';
        //\Yii::getLogger()->log('看看:'.$pay_target,Logger::LEVEL_ERROR);
        if(!OtherPayUtil::GetOtherPayParams($passParams,$pay_type,$pay_target,$out,$error))
        {
            $rst['msg']=$error;
            \Yii::getLogger()->log('web微信支付异常:'.var_export($rst,true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        $bill_no = $out['bill_no'];
        unset($out['bill_no']);
        $rst['msg'] = json_encode($out);
        $rst['bill_no'] = $bill_no;
        echo json_encode($rst);
    }
} 