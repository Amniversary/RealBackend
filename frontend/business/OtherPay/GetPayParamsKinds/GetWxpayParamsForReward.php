<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/30
 * Time: 14:33
 */

namespace frontend\business\OtherPay\GetPayParamsKinds;


use common\components\WeiXinUtil;
use frontend\business\OtherPay\IGetPayParams;
use frontend\business\RewardUtil;
use yii\log\Logger;

class GetWxpayParamsForReward implements IGetPayParams
{
    public function GetPayParams($passParam,&$outParams,&$error)
    {
        if(!isset($passParam) || !is_array($passParam))
        {
            $error = '参数异常';
            return false;
        }
        $out = null;
        if(!RewardUtil::SaveWxpayReward($passParam,$out,$error))
        {
            return false;
        }
        $body = sprintf('reward_id=%s&pay_target=reward',$out['reward_id']);
        $input=[
            'dis'=>'微信支付打赏',
            'body'=>$body,
            'out_trade_no'=>$out['bill_no'],
            'real_pay_money'=>$out['real_pay_money'],
        ];
        if(!WeiXinUtil::GetAppPayParams($input,$outParams,$error))
        {
            \Yii::getLogger()->log('获取微信支付打赏参数失败：'.$error,Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }
} 