<?php
/**
 * 作用跟 GetWxpayParamsForRecharge 类似
 * 为了不修改原先的代码，同时又要满足添加支付账号
 */

namespace frontend\business\OtherPay\GetPayParamsKinds;


use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use common\components\WeiXinUtil;
use frontend\business\ClientGoodsUtil;
use frontend\business\OtherPay\IGetPayParams;
use frontend\business\RechargeListUtil;
use yii\log\Logger;

class GetWxpayParamsForOtherRecharge implements IGetPayParams
{
    public function GetPayParams($passParam,&$outParams,&$error)
    {
        if(!isset($passParam) || !is_array($passParam))
        {
            $error = '参数异常';
            return false;
        }

        //检测参数
        //$goodsInfo = ClientGoodsUtil::GetGoodsInfoById($passParam['goods_id']);
        $fields = ['user_id','goods_id'];
        $fieldLabels = ['用户id','充值金额'];
        $len = count($fields);
        for($i =0; $i <$len; $i ++)
        {
            if(!isset($passParam[$fields[$i]]) && !empty($passParam[$fields[$i]]))
            {
                $error = $fieldLabels[$i].'不能为空';
                return false;
            }
            if(doubleval($passParam[$fields[$i]]) <= 0)
            {
                $error = $fieldLabels[$i].'必须大于0';
                return false;
            }
        }
        $user_id = $passParam['user_id'];
        $goods_id = $passParam['goods_id'];
        $pay_bill = WaterNumUtil::GenWaterNum('ZHF-RGD-',true,true,date('Y-m-d'),4);
        $unique_op_no = UsualFunForStringHelper::CreateGUID();
        $rechargeModel = RechargeListUtil::GetRechageListNewModel($goods_id,'4',$pay_bill,$user_id,$unique_op_no);
        if($rechargeModel === false)
        {
            $error = '该商品不存在';
            return false;
        }
        if(!$rechargeModel->save())
        {
            $error = '微信充值记录保存失败';
            \Yii::getLogger()->log($error.' '.var_export($rechargeModel->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        $charge_id = $rechargeModel->recharge_id;
        $body = sprintf('pay_target=recharge&charge_id=%s&device_type=%s',
            $charge_id,
            $passParam['device_type']
            );
        $out_trade_no =$pay_bill;// $out['bill_no'];
        $input=[
            'dis'=>'微信支付充值',
            'body'=>$body,
            'out_trade_no'=>$out_trade_no,
            'real_pay_money'=>$rechargeModel->goods_price,
        ];
        if(!WeiXinUtil::GetOtherPayParams($input,$outParams,$error))
        {
            return false;
        }
        return true;
    }
} 