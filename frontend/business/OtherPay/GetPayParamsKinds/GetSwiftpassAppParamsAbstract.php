<?php
/**
 * 兴业银行阿里APP支付
 */

namespace frontend\business\OtherPay\GetPayParamsKinds;

use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use frontend\business\OtherPay\IGetPayParams;
use frontend\business\RechargeListUtil;
use yii\log\Logger;

abstract class GetSwiftpassAppParamsAbstract implements IGetPayParams
{

    protected $payType;

    public function GetPayParams($passParam,&$outParams,&$error)
    {
        $user_id = $passParam['user_id'];
        $goods_id = $passParam['goods_id'];
        $pay_bill = WaterNumUtil::GenWaterNum('ZHF-WFT-',true,true,date('Y-m-d'),4);
        $unique_op_no = UsualFunForStringHelper::CreateGUID();
        $rechargeModel = RechargeListUtil::GetRechageListNewModel($goods_id, $this->payType, $pay_bill, $user_id, $unique_op_no);
        if($rechargeModel === false)
        {
            $error = '商品不存在';
            \Yii::error($error . ' goods_id:' . $goods_id);
            return false;
        }
        if(!$rechargeModel->save())
        {
            $error = '充值记录保存失败';
            \Yii::error($error . ' '. var_export($rechargeModel->getErrors(), true));
            return false;
        }
        
        $charge_id = $rechargeModel->recharge_id;
        $attach = sprintf('pay_target=recharge&charge_id=%s&device_type=%s',
            $charge_id,
            $passParam['device_type']
        );

        $swiftpass = new \common\components\swiftpass\Request();
        $parmas = [
            'body'          => $rechargeModel->goods_name,
            'out_trade_no'  => $pay_bill,
            'attach'        => $attach,
            'total_fee'     => $rechargeModel->goods_price * 100,
            'mch_create_ip' => '127.0.0.1',
        ];
        try {
            $outParams = $swiftpass->createUnifiedTrade($parmas, $this->payType);
            $outParams['out_trade_no'] = $pay_bill;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            return false;
        }
        return true;
    }
} 