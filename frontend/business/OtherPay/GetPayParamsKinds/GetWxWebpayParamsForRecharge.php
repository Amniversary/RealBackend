<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:33
 */

namespace frontend\business\OtherPay\GetPayParamsKinds;


use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use common\components\WeiXinUtil;
use common\components\wxpay\JsApiPay;
use common\components\wxpay\lib\WxPayApi;
use common\components\wxpay\lib\WxPayConfig;
use common\components\wxpay\lib\WxPayUnifiedOrder;
use frontend\business\OtherPay\IGetPayParams;
use frontend\business\RechargeListUtil;
use frontend\business\RewardUtil;
use yii\log\Logger;

class GetWxWebpayParamsForRecharge implements IGetPayParams
{
    public function GetPayParams($passParam,&$outParams,&$error)
    {
        if(!isset($passParam) || !is_array($passParam))
        {
            $error = '参数异常';
            return false;
        }
        if(empty($passParam['open_id']))
        {
            $error = 'openid不能为空';
            return false;
        }
        $openId = $passParam['open_id'];// $tools->GetOpenid();
        unset($passParam['open_id']);
        $user_id = $passParam['user_id'];
        $goods_id = $passParam['goods_id'];
        $pay_bill = WaterNumUtil::GenWaterNum('ZHF-RGWEB-',true,true,date('Y-m-d'),4);
        $unique_op_no = UsualFunForStringHelper::CreateGUID();
        if($passParam['goods_type'] == 1)
        {
            $pay_target = 'recharge';
            $rechargeModel = RechargeListUtil::GetRechageListNewModel($goods_id,'100',$pay_bill,$user_id,$unique_op_no);
        }
        else
        {
            $pay_target = 'recharge_gold';
            $rechargeModel = RechargeListUtil::GetRechargeGoldListNewModel($goods_id,'100',$pay_bill,$user_id,$unique_op_no);
        }
        if($rechargeModel === false)
        {
            $error = '该商品不存在';
            return false;
        }
        if(!$rechargeModel->save())
        {
            $error = 'web微信充值记录保存失败';
            \Yii::getLogger()->log($error.' '.var_export($rechargeModel->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        if($passParam['goods_type'] == 1)
        {
            $charge_id = $rechargeModel->recharge_id;
        }
        else
        {
            $charge_id = $rechargeModel->prestore_id;
        }
        $body = sprintf('pay_target=%s&charge_id=%s&device_type=%s',
            $pay_target,
            $charge_id,
            $passParam['device_type']
        );
        $tools = new JsApiPay();
        //\Yii::getLogger()->log('rechargeModel: body:'.$body.'   charge_id:'.$charge_id,Logger::LEVEL_ERROR);

        //②、统一下单
        $notify_url = WxPayConfig::NOTIFY_URL;
        if($passParam['goods_type'] == 1)
        {
            $goodsPrice = doubleval($rechargeModel->goods_price)*100;
        }
        else
        {
            $goodsPrice = doubleval($rechargeModel->gold_goods_price)*100;
        }

        //$goodsPrice = 0.01* 100;
        $input = new WxPayUnifiedOrder();
        $input->SetBody("微信支付充值");
        $input->SetAttach($body);
        $input->SetOut_trade_no($pay_bill);
        $input->SetTotal_fee($goodsPrice);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("微信支付充值");
        $input->SetNotify_url('http://'.$_SERVER['HTTP_HOST'].WxPayConfig::NOTIFY_URL);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        //var_dump($input);exit;
        //\Yii::getLogger()->log('inpuit:'.var_export($input,true),Logger::LEVEL_ERROR);
        $order = WxPayApi::unifiedOrder($input);
        if($order['return_code'] === 'FAIL')
        {
            $error = $order['return_msg'];
            return false;
        }
/*        echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        $this->printf_info($order);*/
        $outParams = $tools->GetJsApiParameters($order,false);
        //\Yii::getLogger()->log('web支付:'.var_export($outParams,true),Logger::LEVEL_ERROR);
        $outParams['bill_no'] = $pay_bill;
        return true;
    }
} 