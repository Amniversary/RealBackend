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
use frontend\business\GoldPay\IGetGoldPayParams;
use frontend\business\OtherPay\IGetPayParams;
use frontend\business\GoldsPrestoreUtil;
use yii\log\Logger;

class GetWxpayParamsForOtherPrestore implements IGetPayParams
{
    public function GetPayParams($passParam,&$outParams,&$error)
    {
        //检测参数

        $fields = ['user_id','gold_goods_id'];
        $fieldLabels = ['用户id','金币商品id'];
        $len = count($fields);
        for($i =0; $i <$len; $i ++){
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
        $gold_goods_id = $passParam['gold_goods_id'];
        $pay_bill = WaterNumUtil::GenWaterNum('ZHF-RGD-',true,true,date('Y-m-d'),4);
        $unique_op_no = UsualFunForStringHelper::CreateGUID();
        $GoldsPrestoreModel = GoldsPrestoreUtil::GetGoldPrestoreModel($gold_goods_id,'4',$pay_bill,$user_id,$unique_op_no);
        if($GoldsPrestoreModel === false)
        {
            $error = '该商品不存在';
            return false;
        }
        if(!$GoldsPrestoreModel->save()){
            $error = '微信充值记录保存失败';
            \Yii::getLogger()->log($error.' '.var_export($GoldsPrestoreModel->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        $prestore_id = $GoldsPrestoreModel->prestore_id;
        $body = sprintf('pay_target=prestore&prestore_id=%s&device_type=%s',
            $prestore_id,
            $passParam['device_type']
        );
        $out_trade_no = $pay_bill;// $out['bill_no'];
        $input=[
            'dis'=>'微信支付充值',
            'body'=>$body,
            'out_trade_no'=>$out_trade_no,
            'real_pay_money'=>$GoldsPrestoreModel->pay_money,
        ];
        if(!WeiXinUtil::GetOtherPayParams($input,$outParams,$error))
        {
            return false;
        }
        return true;
    }
} 