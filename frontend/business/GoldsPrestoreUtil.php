<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/7
 * Time: 20:17
 */

namespace frontend\business;

use yii;
use backend\business\CheckWeCatOrderForm;
use backend\business\GoodsUtil;

use common\components\alipay\AlipayUtil;
use common\components\tenxunlivingsdk\TimRestApi;
use common\components\UsualFunForStringHelper;
use common\models\GoldsGoods;
use common\models\GoldsPrestore;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use frontend\business\SaveRecordByransactions\SaveByTransaction\PrestoreSaveByTrans;
use yii\base\Exception;
use yii\db\Query;
use yii\db\Transaction;
use yii\log\Logger;

class GoldsPrestoreUtil
{

    /**
     * 获取支付宝支付，未知状态记录,大于15分钟状态任然未知，需要处理
     */
    public static function GetUnkownOtherPayRechargeRecords($limit = 100)
    {
        $query = GoldsPrestore::find();
        $query->select(['prestore_id','status_result','pay_type','pay_bill']);
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
                if(!GoldsPayUtil::DealGoldPayResult($params,$pay_type,$pay_target,$error)){             
                    \Yii::getLogger()->log('2支付通知时结果处理异常：'.$error,Logger::LEVEL_ERROR);
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * 获取金币充值记录模型
     * @param $pay_type
     * @param $pay_bill
     * @param $user_id
     * @param $uniqueNo
     * @return GoldsPrestore Model
     */
    public static function GetGoldPrestoreModel($gold_goods_id,$pay_type,$pay_bill,$user_id,$uniqueNo)
    {
        $goldGoods = GoldsGoodsUtil::GetGoldGoodsModelOne($gold_goods_id);
        //\Yii::getLogger()->log('goldGoods:'.var_export($goldGoods,true),Logger::LEVEL_ERROR);
        if(!($goldGoods instanceof GoldsGoods))
        {
            return false;
        }
        try
        {
            $model = new GoldsPrestore();
            $model->pay_type = $pay_type;
            $model->pay_bill = $pay_bill;
            $model->user_id = $user_id;
            $model->pay_times = 1;
            $model->create_time = date('Y-m-d H:i:s');
            $model->status_result = 1;//支付中
            $model->op_unique_no = $uniqueNo;
            $model->gold_goods_id   = $goldGoods->gold_goods_id;
            $model->gold_goods_name = $goldGoods->gold_goods_name;
            $model->gold_goods_price = $goldGoods->gold_goods_price;//0.01
            $model->gold_goods_num = $goldGoods->gold_num;
            $model->extra_integral_num = $goldGoods->extra_integral_num;
            $model->pay_money = $goldGoods->gold_goods_price;//0.01
        }
        catch(Exception $e)
        {
            \Yii::getLogger()->log('goldGoods:'.var_export($model,true),Logger::LEVEL_ERROR);
            \Yii::getLogger()->log('goldGoods-error:'.$e->getMessage(),Logger::LEVEL_ERROR);
        }

        return $model;
    }

    /*
     * 根据账单号获取金币充值信息
     * @param $bill_no
     * @return null|static
     */
    public static function GetGoldPrestoreModelByBillNo($bill_no){
        return GoldsPrestore::findOne(['pay_bill'=>$bill_no]);
    }

    /*
     * 根据充值id 获取充值信息
     * @param $prestore_id
     */
    public static function GetGoldPrestoreModelById($prestore_id){
         return GoldsPrestore::findOne(['prestore_id'=>$prestore_id]);
    }
    
    /**
     * 事物保存支持
     * @param $objList  需要保存的对象数组，
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function GoldPrestoreSaveByTransaction($objList,&$outInfo,&$error)
    {
        $error ='';
        if(!isset($objList) || !is_array($objList))
        {
            $error = '非法对象，不是数组';
            return false;
        }
        foreach($objList as $obj)
        {
            if(!($obj instanceof ISaveForTransaction))
            {
                $error = '对象数组中存在非ISaveForTransaction对象';
                return false;
            }
        }
        $outInfo = []; 
        $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);
        try
        {
            foreach($objList as $obj)
            {     
                if(!$obj->SaveRecordForTransaction($error,$outInfo))
                {
                    if(is_array($error))
                    {
                        \Yii::getLogger()->log(var_export($error,true).' type:'.var_export($obj,true),Logger::LEVEL_ERROR);
                    }
                    else
                    {
                        \Yii::getLogger()->log($error.' type:'.var_export($obj,true),Logger::LEVEL_ERROR);
                    }
                    $trans->rollBack();
                    return false;
                }
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $trans->rollBack();
            return false;
        }
        return true;
    }
    
    /**
     * 获取充值账单支付状态
     * @param $prestore
     * @param $recharge_status
     * @param $out
     * @param $pay_type
     * @param $user
     * @param $error
     */
    public static function GetPrestoreRecodeStatus($prestore, $prestore_status, $out, $pay_type ,&$error){
        $goldsAccountModel = GoldsAccountUtil::GetGoldsAccountModleByUserId($prestore->user_id);
        $client = ClientUtil::GetClientById($prestore->user_id);
        $type = '';
        switch($pay_type) {
            case 3: $type = '支付宝'; break;
            case 4: $type = '微信app端'; break;
            case 6: $type = '苹果'; break;
            case 100: $type = '微信web端'; break;
        }

        if(!isset($goldsAccountModel)){
            $error = '用户金币账户信息不存在';
            return false;
        }

        if($prestore_status == 3){
            $error = $type.'账单签名错误';
            return false;
        }

        if($prestore_status != 1){
            switch($prestore_status)
            {
                case 0:
                    $error = $type.'账单记录不存在';
                    break;

                case 4:
                    $error = $type.'账单交易失败';
                    break;
            }
            $prestore->status_result = 0;
            $prestore->fail_reason = $error;
            
            if($pay_type == 4 || $pay_type == 100){
                if(isset($out['trade_state_desc'])){
                    $error = $out['trade_state_desc'];
                    $prestore->fail_reason = $out['trade_state_desc'];
                }
            }
            if(!$prestore->save())
            {
                $error = '保存结果失败';
                \Yii::getLogger()->log($error.': '.var_export($prestore->getErrors(),true),Logger::LEVEL_ERROR);
                return false;
            }
            return false;
        }

        $params=[
            'trade_no'=>$out['trade_no'],
            'trade_ok'=>'2',
            'out_trade_no'=>$out['out_trade_no'],
            'total_fee'=>$out['total_fee'],
            'prestore_id'=>$prestore->prestore_id,
            'device_type'=>$client->device_type,
        ];
        
        if(isset($out['openid'])){
            $params['open_id'] = $out['openid'];
        }
        
        $pay_target = 'prestore';   //OtherPayUtil::DealOtherPayResult($params,$pay_type,$pay_target,$error)
        
        if(!GoldsPayUtil::DealGoldPayResult($params, $pay_type, $pay_target, $error)){
            return false;
        }
        return true;
    }

} 