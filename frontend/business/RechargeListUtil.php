<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/7
 * Time: 20:17
 */

namespace frontend\business;


use backend\business\CheckWeCatOrderForm;
use common\components\alipay\AlipayUtil;
use common\components\tenxunlivingsdk\TimRestApi;
use common\components\UsualFunForStringHelper;
use common\models\GoldsGoods;
use common\models\GoldsPrestore;
use common\models\Goods;
use common\models\Recharge;
use common\models\RechargeList;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RechargeListRecordSaveByTrans;
use yii\log\Logger;

class RechargeListUtil
{

    /**
     * 获取支付宝支付，未知状态记录,大于15分钟状态任然未知，需要处理
     */
    public static function GetUnkownOtherPayRechargeRecords($limit = 100)
    {
        $query = RechargeList::find();
        $query->select(['recharge_id','status_result','pay_type','pay_bill']);
        $query->limit($limit);
        $query->where(['and',['status_result'=>'1','pay_type'=>'3'],['<','create_time',date('Y-m-d H:i:s',strtotime('-15 minutes'))]]);
        return $query->all();
    }

    /**
     * 处理未知支付宝交易记录
     * @param $tradeInfo
     * @param $recharge
     * @param $error
     */
    public static function DealUnkownAliPayPayResult($tradeInfo,&$error)
    {
        if(!is_array($tradeInfo))
        {
            $error = '不是正确的支付结果参数';
            return false;
        }
        $params = [
            'trade_no'=>isset($tradeInfo['trade_no'])?$tradeInfo['trade_no']:'',
            'trade_ok'=>'2',
            'out_trade_no'=>isset($tradeInfo['out_trade_no'])?$tradeInfo['out_trade_no']:'',
            'body'=>isset($tradeInfo['body'])?$tradeInfo['body']:'',
            'total_fee'=>isset($tradeInfo['total_fee'])?$tradeInfo['total_fee']:'',
        ];
        $pay_type = '3';
        if(empty($params['body']))
        {
            $error = '2支付宝支付订单body参数为空';
            \Yii::getLogger()->log('2支付宝支付订单body参数为空：'.var_export($tradeInfo,true),Logger::LEVEL_ERROR);
            return false;
        }
        else
        {
            $body = $params['body'];
            $parItems = explode('&',$body);
            $len = count($parItems);
            for($i = 0; $i < $len; $i++ )
            {
                $items = explode('=',$parItems[$i]);
                if(count($items) === 2)
                {
                    $params[$items[0]] = $items[1];
                }
                else
                {
                    $error = '2支付通知时body参数解析异常，出现多个等号';
                    \Yii::getLogger()->log('2支付通知时body参数解析异常，出现多个等号，原来参数：'.$parItems[$i],Logger::LEVEL_ERROR);
                    return false;
                }
            }
            unset($params['body']);
            if(!isset($params['pay_target']) || empty($params['pay_target']))
            {
                $error = '2支付通知时body参数解析异常，pay_target参数为空';
                \Yii::getLogger()->log('2支付通知时body参数解析异常，pay_target参数为空',Logger::LEVEL_ERROR);
                return false;
            }
            else
            {
                $pay_target = $params['pay_target'];
                unset($params['pay_target']);
                $error = '';
                //\Yii::getLogger()->log('进入支付通知时结果处理 params：'.var_export($params,true),Logger::LEVEL_ERROR);
                if(!OtherPayUtil::DealOtherPayResult($params,$pay_type,$pay_target,$error))
                {
                    \Yii::getLogger()->log('2支付通知时结果处理异常：'.$error,Logger::LEVEL_ERROR);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 根据id查找金币充值记录
     * @param $prestore_id
     * @return null|static
     */
    public static function GetChargeGoldListById($prestore_id)
    {
        return GoldsPrestore::findOne(['prestore_id'=>$prestore_id]);
    }

    /**
     * 根据id查找还款记录
     * @param $charge_id
     * @return null|static
     */
    public static function GetChargeListById($charge_id)
    {
        return Recharge::findOne(['recharge_id'=>$charge_id]);
    }
    /**
     * 获取充值记录模型
     * @param $pay_money
     * @param $pay_type
     * @param $pay_bill
     * @param $user_id
     * @param $uniqueNo
     * @return Recharge
     */
    public static function GetRechageListNewModel($goods_id,$pay_type,$pay_bill,$user_id,$uniqueNo)
    {
        $goods = GoodsUtil::GetGoodsById($goods_id);
        if(!($goods instanceof Goods))
        {
            return false;
        }
        $model = new Recharge();
        $model->pay_type = $pay_type;
        $model->pay_bill = $pay_bill;
        $model->user_id = $user_id;
        $model->pay_times = 1;
        $model->create_time = date('Y-m-d H:i:s');
        $model->status_result = 1;//支付中
        $model->op_unique_no = $uniqueNo;
        $model->goods_id = $goods->goods_id;
        $model->goods_name = $goods->goods_name;
        $model->goods_price = $goods->goods_price;//0.01
        $model->goods_num = 1;
        $model->bean_num = $goods->bean_num;
        $model->pay_money = $goods->goods_price;// 0.01
        return $model;
    }

    /**
     * 获取金币充值记录模型
     * @param $goods_id
     * @param $pey_type
     * @param $pay_bill
     * @param $user_id
     * @param $uniqueNo
     * @return Recharge
     */
    public static function GetRechargeGoldListNewModel($goods_id,$pey_type,$pay_bill,$user_id,$uniqueNo)
    {
        $gold_goods = GoldsGoodsUtil::GetGoldGoodsModelOne($goods_id);
        if(!($gold_goods instanceof GoldsGoods))
        {
            return false;
        }
        $model = new GoldsPrestore();
        $model->pay_type = $pey_type;
        $model->pay_bill = $pay_bill;
        $model->user_id = $user_id;
        $model->pay_times = 1;
        $model->create_time = date('Y-m-d H:i:s');
        $model->status_result = 1;//支付中
        $model->op_unique_no = $uniqueNo;
        $model->gold_goods_id = $gold_goods->gold_goods_id;
        $model->gold_goods_name = $gold_goods->gold_goods_name;
        $model->gold_goods_price = $gold_goods->gold_goods_price;
        $model->gold_goods_num = $gold_goods->gold_num;
        $model->pay_money = $gold_goods->gold_goods_price;
        $model->extra_integral_num = $gold_goods->extra_integral_num;
        return $model;
    }


    /**
     * 根据账单号获取充值信息
     * @param $bill_no
     * @return null|static
     */
    public static function GetRechargeInfoByBillNo($bill_no)
    {
        return Recharge::findOne(['pay_bill'=>$bill_no]);
    }

    /**
     * 根据账单号获取金币充值信息
     * @param $bill_no
     * @return null|static
     */
    public static function GetRechargeGoldByBillNo($bill_no)
    {
        return GoldsPrestore::findOne(['pay_bill'=>$bill_no]);
    }

    /**
     * 根据充值id 获取充值信息
     * @param $recharge_id
     */
    public static function GetRachargeById($recharge_id)
    {
        return Recharge::findOne(['recharge_id'=>$recharge_id]);
    }


    /**
     * 获取充值账单支付状态
     * @param $recharge
     * @param $recharge_status
     * @param $out
     * @param $pay_type
     * @param $user
     * @param $error
     */
    public static function GetRechargeRecodeStatus($recharge, $recharge_status, $out, $pay_type ,&$error)
    {
        $balance = BalanceUtil::GetUserBalanceByUserId($recharge->user_id);
        $client = ClientUtil::GetClientById($recharge->user_id);
        $type = '';
        switch($pay_type) {
            case 3: $type = '支付宝'; break;
            case 4: $type = '微信app端'; break;
            case 6: $type = '苹果'; break;
            case 100: $type = '微信web端'; break;
        }

        if(!isset($balance))
        {
            $error = '用户账户信息不存在 ';

            return false;
        }

        if($recharge_status == 3)
        {
            $error = $type.'账单签名错误 ';
            return false;
        }

        if($recharge_status != 1)
        {
            switch($recharge_status)
            {
                case 0:
                    $error = $type.'账单记录不存在 ';
                    break;
                case 4:		
                    $error = $type.'账单交易失败 ';
                    break;
                case 5:
                    $error = $type.'充值取消支付或已经支付';
                    break;
            }
            $recharge->status_result = 0;
            $recharge->fail_reason = $error;
            if($pay_type == 4 || $pay_type == 100){
                if(isset($out['trade_state_desc'])){
                    $error .= $out['trade_state_desc'];
                    $recharge->fail_reason = $out['trade_state_desc'];
                }
            }
            if(!$recharge->save())
            {
                $error = '保存结果失败';
                \Yii::getLogger()->log($error.': '.var_export($recharge->getErrors(),true),Logger::LEVEL_ERROR);
                return false;
            }
            return false;
        }

        $params=[
            'trade_no'=>$out['trade_no'],
            'trade_ok'=>'2',
            'out_trade_no'=>$out['out_trade_no'],
            'total_fee'=>$out['total_fee'],
            'charge_id'=>$recharge->recharge_id,
            'device_type'=>$client->device_type,
        ];
        if(isset($out['openid']))
        {
            $params['open_id'] = $out['openid'];
        }
        $pay_target = 'recharge';
        if(!OtherPayUtil::DealOtherPayResult($params,$pay_type,$pay_target,$error))
        {
            return false;
        }
        //亲爱的用户，由于网络延迟原因，您充值的60鲜花现已到账，请到“我的豆”页面核实，若还有问题请加官方客服QQ群374529167咨询
        $phone = \Yii::$app->params['service_tel'];
        $text_content = '亲爱的用户，由于网络延迟原因，您充值的'.$recharge->bean_num.'鲜花现已到账，请到"我的鲜花"页面核实，若还有问题请加官方客服QQ群'.$phone.'咨询';
        if(!TimRestApi::openim_send_Text_msg($recharge->user_id,$text_content,$error))
        {
            \Yii::getLogger()->log('发送腾讯云通知消息异常: '.$error,Logger::LEVEL_ERROR);
            $error = '充值成功，发送腾讯云私信失败!';
            return false;
        }
        return true;
    }

} 