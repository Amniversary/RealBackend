<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-4-29
 * Time: 下午5:00
 */

namespace frontend\business;

use common\components\PhpLock;
use common\components\UsualFunForStringHelper;
use common\models\LuckygiftParams;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\TicketMyMoneyTrans;
use yii\db\Query;
use yii\log\Logger;

class LuckyGiftUtil
{
    /**
     * 根据幸运礼物ID获取详情
     */
    public static function GetLuckyGiftById($lucky_id){
        return LuckygiftParams::findOne(['lucky_id'=>$lucky_id]);
    }

    /**
     * 启用、禁用幸运礼物概率
     * @param $luckygift
     * @return bool
     */
    public static function SetStatus($luckygift,&$error)
    {
        if(!($luckygift instanceof LuckygiftParams))
        {
            $error = '不是幸运礼物记录';
            return false;
        }
        if(!$luckygift->save())
        {
            $error = '启用/禁用幸运礼物保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($luckygift->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        if(!LuckyGiftUtil::DeleteLuckyGiftCache($error))
        {
            return false;
        }
        return true;
    }

    /**
     * 删除幸运礼物缓存
     * @return bool
     */
    public static function DeleteLuckyGiftCache()
    {
        $cache = \Yii::$app->cache->delete('luckgift_list_info');
        if(!$cache)
        {
            \Yii::getLogger()->log('删除幸运礼物缓存失败',Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 将幸运礼物概率信息写入缓存
     * @param $error
     * @param $outInfo
     * @return bool
     */
    public static function SetLuckGiftCache()
    {
        $query_result = (new Query())
            ->select(['lucky_id','receive_rate','basic_beans','multiple','rate','status','create_time'])
            ->from('mb_luckygift_params')
            ->where('status=1')->all();

        $jsonData = json_encode($query_result);
        \Yii::$app->cache->set('luckgift_list_info',$jsonData);
        return $jsonData;
    }

    /**
     * 幸运礼物概率信息
     * @return mixed
     */
    public static function getLuckyGiftCache()
    {
        $cnt = \Yii::$app->cache->get('luckgift_list_info');
        if($cnt == false){
            $lock = new PhpLock('mb_lock_luckgift_list_info');
            $lock->lock();
            $cnt = \Yii::$app->cache->get('luckgift_list_info');
            if($cnt == false){
                $cnt = self::SetLuckGiftCache();
            }
            $lock->unlock();
        }
        $rst = json_decode($cnt,true);
        return $rst;
    }

    /**
     * 判断是否有概率获得幸运礼物，并计算出对应主播所得豆数
     * @param $user_id
     * @param $other_id
     * @param $gift_value
     * @param $outInfo
     * @param $error
     * @return bool
     */
//    public static function GetLuckGiftRate($user_id,$other_id,$gift_value,$level_no,$nick_name,&$outInfo,&$error)
//    {
    public static function GetLuckGiftRate($banlances_object,$params,&$outInfo,&$error)
    {
        // 获取礼物概率信息
        $lucky_gift_info = \Yii::$app->cache->get('luckgift_list_info');
        if($lucky_gift_info === false)
        {
            $phpLock = new PhpLock('mb_lock_luckgift_list_info');
            $phpLock->lock();
            $lucky_gift_info = \Yii::$app->cache->get('luckgift_list_info');
            if($lucky_gift_info === false)
            {
                $lucky_res = self::SetLuckGiftCache();
                if(!$lucky_res)
                {
                    $phpLock->unlock();
                    return false;
                }
                $lucky_gift_info = json_decode($lucky_res,true);
            }
            else
            {
                $lucky_gift_info = json_decode($lucky_gift_info,true);
            }
            $phpLock->unlock();
        }
        else
        {
            $lucky_gift_info = json_decode($lucky_gift_info,true);
        }
        if(!isset($lucky_gift_info) || empty($lucky_gift_info))
        {
            $outInfo['gift_value'] = $params['gift_value'];        //无幸运礼物信息，返回原豆值
            $outInfo['multiple'] = 1;
            $outInfo['total_gift_value'] = $params['gift_value'];
            $outInfo['receive_rate'] = 1;
            return true;
        }
        if(!self::GetPassRateInfo($params['gift_value'],$lucky_gift_info,$outInfo,$error))
        {
            return true;
        }
        if(!isset($outInfo['rate_list']) && empty($outInfo['rate_list']))
        {
            $outInfo['gift_value'] = $params['gift_value'];        //无幸运礼物信息，返回原豆值
            $outInfo['multiple'] = 1;
            $outInfo['total_gift_value'] = $params['gift_value'];
            $outInfo['receive_rate'] = 1;
            return true;
        }

        if($outInfo['is_rate_100'] != 1)
        {
            $rate_list_key = self::GetRandRate($outInfo['rate_list'],$outInfo['float_len']);
            array_pop($outInfo['rate_list']);
            $lucky_rate_one = $outInfo['rate_list'];   //去除未获得的机率

            if(!isset($lucky_rate_one[$rate_list_key]) && empty($lucky_rate_one[$rate_list_key]))         //判断有无获得幸运礼物
            {
                $outInfo['gift_value'] = $params['gift_value'];        //未获得幸运礼物，返回原豆值
                $outInfo['multiple'] = 1;  // 倍数
                $outInfo['total_gift_value'] = $params['gift_value'];
                $outInfo['receive_rate'] = 1;
                return true;
            }
        }
        else
        {
            $rate_list_key =  $outInfo['is_rate_100_key'];    //百分百机率获得
        }
        $lucky_gift_rate_info = $lucky_gift_info[$rate_list_key];  //获得的幸运礼物信息
        if($lucky_gift_rate_info['receive_rate'] <= 0)
        {
            $lucky_gift_rate_info['receive_rate'] = 1;
        }
        if($lucky_gift_rate_info['multiple'] <= 0)
        {
            $lucky_gift_rate_info['multiple'] = 1;
        }
//        $new_gift_value = $gift_value*$lucky_gift_rate_info['multiple']*($lucky_gift_rate_info['receive_rate']/100);  //计算 主播所得豆票值
        $new_gift_value = $params['gift_value'] * ($lucky_gift_rate_info['receive_rate'] / 100);  //计算 主播所得豆票值
        $lucky_gift_value = intval($params['gift_value']*$lucky_gift_rate_info['multiple']);
        $multiple = $lucky_gift_rate_info['multiple'];
        $total_gift_value = intval($new_gift_value);
        $receive_rate = $lucky_gift_rate_info['receive_rate'];

        $client_info = ClientUtil::GetClientById($params['user_id']);
        $pic = empty($client_info->icon_pic)?$client_info->pic:$client_info->icon_pic;

        /**当前用户获得幸运礼物**/
        $money_params = [
            'bean_num' => $lucky_gift_value,
            'user_id' => $params['user_id'],
        ];

        $extend_params = [
            'unique_id' => UsualFunForStringHelper::CreateGUID(),
            'op_value' => $lucky_gift_value,
            'relate_id' => '',
            'money_type' => 1,
        ];
        $transActions[] = new ModifyBalanceByAddRealBean($banlances_object,$money_params);
        $extend_params['field'] = 'bean_balance';
        $extend_params['operate_type'] = 24;
        $extend_params['device_type'] = $params['device_type'];
        $transActions[] = new CreateUserBalanceLogByTrans($banlances_object,$extend_params);
        if (!RewardUtil::RewardSaveByTransaction($transActions, $outInfo, $error))
        {
            \Yii::getLogger()->log('当前用户中了幸运礼物，banlances修改失败',Logger::LEVEL_ERROR);
            return false;
        }
        /***向群发送消息***/
        $im_data = [
            'key_word'=>'send_lucky_gift_im',
            'user_id' => $params['user_id'],
            'nick_name' => $params['nick_name'],
            'level_no' => $params['level_no'],
            'pic' => $pic,
            'multiple' => $lucky_gift_rate_info['multiple'],
            'total_beans' => $lucky_gift_value,
            'other_id' => $params['other_id']
        ];
        \Yii::getLogger()->log('幸运礼物礼物IMDATA===:'.var_export($im_data,true),Logger::LEVEL_ERROR);
        $outInfo['gift_value'] = $params['gift_value'];
        $outInfo['lucky_gift_value'] = $lucky_gift_value;
        $outInfo['multiple'] = $multiple;
        $outInfo['total_gift_value'] = $total_gift_value;
        $outInfo['receive_rate'] = $receive_rate;
        if(!JobUtil::AddImJob('tencent_im',$im_data,$error))
        {
            \Yii::getLogger()->log('幸运礼物im消息发送失败：'.'fail:'.$error.' date_time:'.date('Y-m-d H:i:s'),Logger::LEVEL_ERROR);
        }
        return true;
    }

    /**
     * 判断是否有概率获得幸运礼物，并计算出对应主播所得豆数
     * @param $balanceData
     * @param $params
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public static function GetLuckGiftRateTest($balanceData,$params,&$outInfo,&$error)
    {
        //TODO: 获取礼物概率信息
        $luckChance = self::getLuckyGiftCache();
        $giftValue = $params['gift_value'];

        //TODO: 无幸运礼物信息，返回原豆值 初始化倍数
        $outInfo['gift_value'] = $giftValue;
        $outInfo['multiple'] = 1;
        $outInfo['hostValue'] = $giftValue;
        $outInfo['receive_rate'] = 1;

        //TODO: 计算中奖概率 未中奖直接返回原礼物数据
        if(!self::GetPassRateInfoTest($giftValue,$luckChance,$outInfo,$error)) {
            return true;
        }

        if($outInfo['is_rate_100'] != 1)
        {
            $rateKey = self::GetRandRate($outInfo['rate_list'],$outInfo['float_len']);
            array_pop($outInfo['rate_list']);
            $luckyOne = $outInfo['rate_list'];   //去除未获得的机率

            if(!isset($luckyOne[$rateKey]) &&
                empty($luckyOne[$rateKey])) {  //判断有无获得幸运礼物
                return true;
            }
        }
        else
        {
            $rateKey =  $outInfo['is_rate_100_key'];    //百分百机率获得
        }
        $luckyGiftRate = $luckChance[$rateKey];  //获得的幸运礼物信息
        if($luckyGiftRate['receive_rate'] <= 0) {
            $luckyGiftRate['receive_rate'] = 1;
        }
        if($luckyGiftRate['multiple'] <= 0) {
            $luckyGiftRate['multiple'] = 1;
        }

        $hostValue = intval($giftValue * ($luckyGiftRate['receive_rate'] / 100)); //TODO: 计算 主播所得豆票值
        $luckyValue = intval($giftValue * $luckyGiftRate['multiple']); //TODO: 用户所得豆
        $multiple = $luckyGiftRate['multiple'];
        $receive_rate = $luckyGiftRate['receive_rate'];

        $money_params = [
            'bean_num' => $luckyValue,
            'user_id' => $params['user_id'],
        ];

        $extend_params = [
            'unique_id' => UsualFunForStringHelper::CreateGUID(),
            'op_value' => $luckyValue,
            'relate_id' => '',
            'field'=>'bean_balance',
            'operate_type'=>24,
            'device_type'=>$params['device_type']
        ];
        //TODO: 增加幸运礼物得到豆
        $transActions[] = new ModifyBalanceByAddRealBean($balanceData,$money_params);
        //TODO: 增加收到幸运礼物财务日志
        $transActions[] = new CreateUserBalanceLogByTrans($balanceData,$extend_params);
        if (!SaveByTransUtil::RewardSaveByTransaction($transActions, $error, $outInfo)) {
            \Yii::error($error);
            return false;
        }

        //TODO: 向聊天室发送im消息
        $im_data = [
            'key_word'=>'send_lucky_gift',
            'user'=>['id'=>$params['user_id'], 'name'=>$params['nick_name'], 'icon'=>$params['pic']],
            'type'=>203,
            'extra'=>['level_no' => $params['level_no'], 'multiple' => $luckyGiftRate['multiple'], 'total_beans' => $luckyValue],
            'other_id' => $params['other_id']
        ];
        //\Yii::error('幸运礼物礼物IMDATA===:'.var_export($im_data,true));
        $outInfo['gift_value'] = $params['gift_value'];
        $outInfo['luckyValue'] = $luckyValue;
        $outInfo['multiple'] = $multiple;
        $outInfo['hostValue'] = $hostValue;
        $outInfo['receive_rate'] = $receive_rate;
        if(!JobUtil::AddImJob('tencent_im',$im_data,$error))
        {
            \Yii::error('幸运礼物im消息发送失败：'.'fail:'.$error.' date_time:'.date('Y-m-d H:i:s'));
        }
        return true;
    }

    /**
     * 获取通过验证的幸运礼物概率信息
     * @param $gift_value
     * @param $luckygift_arr
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public static function GetPassRateInfo($gift_value,$luckygift_arr,&$outInfo,&$error)
    {
        if(!is_array($luckygift_arr))
        {
            \Yii::getLogger()->log('验证幸运礼物概率信息参数类型错误',Logger::LEVEL_ERROR);
            $error = '参数类型错误';
            return false;
        }
        if(empty($luckygift_arr))
        {
            \Yii::getLogger()->log('幸运礼物概率数据不能为空',Logger::LEVEL_ERROR);
            $error = '幸运礼物概率数据不能为空';
            return false;
        }

        $rate_arr = [];
        $float_len = 0;
        $flag = 0;
        $k = 0;
        foreach($luckygift_arr as $key=>$bean)
        {
            if($gift_value >= $bean['basic_beans'])     //过滤不满足豆条件的数据，豆值必须大于等于基本豆，才有机会获得幸运礼物
            {
                if($bean['rate'] >= 100)    //机率如果为100%直接退出循环
                {
                    $rate_arr = [];
                    $rate_arr[$key] = floatval($bean['rate']);
                    $flag = 1;
                    $k = $key;
                    break;
                }
                $rate_arr[$key] = floatval($bean['rate']);
            }
        }
        if(!isset($rate_arr) || empty($rate_arr))           //豆数没有满足条件
        {
            return false;
        }
        if($flag != 1)      //未出现100%的概率
        {
            $rand_max = count($rate_arr)-1;
            $rand_data =  mt_rand(0,$rand_max);      //随机取出某个满足条件的概率值
            $k = $rand_data;
            $rand_data = $rate_arr[$rand_data];
            $rate_arr = [];
            $rate_arr[$k] = $rand_data;

            $rate_arr[] = 100-$rand_data;  //未出现的机率 百分比
            $temp = explode ( '.', $rand_data );
            $float_len = (($float_len < strlen($temp[1]))?strlen($temp[1]):$float_len);
        }
        else
        {
            $rate_arr[] = 0;  //未出现的机率 百分比
        }
        $outInfo['float_len'] = $float_len;
        $outInfo['rate_list'] = $rate_arr;
        $outInfo['is_rate_100'] = $flag;       //100%机率标识
        $outInfo['is_rate_100_key'] = $k;      //100%机率的数据源key值
        return true;
    }


    /**
     * 获取通过验证的幸运礼物概率信息
     * @param $giftValue
     * @param $luckChance
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public static function GetPassRateInfoTest($giftValue,$luckChance,&$outInfo,&$error)
    {
        if(!is_array($luckChance)) {
            $error = '参数类型错误 :Lucky Params Error';
            \Yii::error($error);
            return false;
        }
        if(empty($luckChance)) {
            $error = '参数异常 : Lucky Params is Empty';
            \Yii::error($error);
            return false;
        }
        //[
        //  'lucky_id' => 1
        //  'receive_rate' => 50
        //  'basic_beans' => 1
        //  'multiple' => 5
        //  'rate' => 100
        //  'status' => 1
        //  'create_time' => 2016-09-29 10:15:36]

        $rate_arr = [];
        $float_len = 0;
        $flag = 0;
        $k = 0;
        foreach($luckChance as $key=>$bean)
        {
            //TODO: 判断赠送金额是否满足 奖励条件，过滤不满足豆条件的数据，豆值必须大于等于基本豆，才有机会获得幸运礼物
            if($giftValue >= $bean['basic_beans']) {
                if($bean['rate'] >= 100)    //TODO: 概率如果为100%直接退出循环
                {
                    $rate_arr[$key] = floatval($bean['rate']);
                    $flag = 1;
                    $k = $key;
                    break;
                }
                $rate_arr[$key] = floatval($bean['rate']);
            }
        }

        //TODO: 礼物金额没有满足中奖条件
        if(!isset($rate_arr) &&
            empty($rate_arr)) {
            return false;
        }
        if($flag != 1)      //未出现100%的概率
        {
            $count = count($rate_arr)-1;
            $randNum =  mt_rand(0,$count);  //TODO: 随机取出一个满足中奖条件的概率值下标
            $k = $randNum;
            $rand_data = $rate_arr[$randNum];  //TODO: 中奖概率
            $rate_arr = [];
            $rate_arr[$k] = $rand_data;

            $rate_arr[] = 100-$rand_data;  //TODO: 未中奖概率 百分比
            $temp = explode ( '.', $rand_data );
            $float_len = (($float_len < strlen($temp[1]))?strlen($temp[1]):$float_len);
        }
        else
        {
            $rate_arr[] = 0;  //TODO: 未中奖概率 百分比 0
        }

        $outInfo['float_len'] = $float_len;
        $outInfo['rate_list'] = $rate_arr;  //TODO: 下标0 为中奖概率
        $outInfo['is_rate_100'] = $flag;    //TODO: 100%机率标识
        $outInfo['is_rate_100_key'] = $k;   //TODO: 中奖概率信息的key  100%机率的数据源key值
        return true;
    }
    /**
     * 多个概率信息中筛选一个
     * @param $proArr
     * @param $float_len
     * @return int|string
     */
    public static function GetRandRate($proArr,$float_len) {
        $result = '';
        $proSum = array_sum($proArr);
        foreach ($proArr as $key => $proCur) {
            $randNum = sprintf("%.".$float_len."f", mt_rand() / mt_getrandmax() * $proSum );
            if ($randNum <= $proCur) {  //TODO: 小于第一个概率 说明中奖
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }
}