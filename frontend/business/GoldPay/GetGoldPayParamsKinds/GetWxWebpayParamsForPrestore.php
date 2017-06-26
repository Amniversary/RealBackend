<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:33
 */

namespace frontend\business\GoldPay\GetGoldPayParamsKinds;


use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use common\components\WeiXinUtil;
use common\components\wxpay\JsApiPay;
use common\components\wxpay\lib\WxPayApi;
use common\components\wxpay\lib\WxPayConfig;
use common\components\wxpay\lib\WxPayUnifiedOrder;
use frontend\business\GoldPay\IGetGoldPayParams;
use frontend\business\RechargeListUtil;
use frontend\business\RewardUtil;
use frontend\business\GoldsPrestoreUtil;
use yii\log\Logger;

class GetWxWebpayParamsForPrestore implements IGetGoldPayParams
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
        $gold_goods_id = $passParam['gold_goods_id'];
        $pay_bill = WaterNumUtil::GenWaterNum('ZHF-RG-',true,true,date('Y-m-d'),4);
        $unique_op_no = UsualFunForStringHelper::CreateGUID();
        $GoldsPrestoreModel = GoldsPrestoreUtil::GetGoldPrestoreModel($gold_goods_id,'4',$pay_bill,$user_id,$unique_op_no);
        
        if($GoldsPrestoreModel === false)
        {
            $error = '该商品不存在';
            return false;
        }
        if(!$GoldsPrestoreModel->save()){
            $error = 'web微信充值记录保存失败';
            \Yii::getLogger()->log($error.' '.var_export($GoldsPrestoreModel->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        $prestore_id = $GoldsPrestoreModel->prestore_id;
        $body = sprintf('pay_target=presore&prestore_id=%s&device_type=%s',
            $prestore_id,
            $passParam['device_type']
        );
        $tools = new JsApiPay();


        //②、统一下单
        $notify_url = WxPayConfig::NOTIFY_URL;

        $GoldGoodsPrice = doubleval($GoldsPrestoreModel->gold_goods_price)*100;
        //$goodsPrice = 0.01* 100;
        $input = new WxPayUnifiedOrder();
        $input->SetBody("微信支付充值");
        $input->SetAttach($body);
        $input->SetOut_trade_no($pay_bill);
        $input->SetTotal_fee($GoldGoodsPrice);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("微信支付充值");
        $input->SetNotify_url('http://'.$_SERVER['HTTP_HOST'].WxPayConfig::NOTIFY_URL);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        //var_dump($input);exit;
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