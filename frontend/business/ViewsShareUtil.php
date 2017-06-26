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
use common\models\ViewsParams;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use yii\db\Query;
use yii\helpers\Console;
use yii\log\Logger;

class ViewsShareUtil
{
    /**
     * 根据直播概率分享表ID获取详情
     */
    public static function GetViewsById($view_id){
        return ViewsParams::findOne(['view_id'=>$view_id]);
    }

    /**
     * 启用、禁用直播分享概率
     * @param $luckygift
     * @return bool
     */
    public static function SetStatus($living_share,&$error)
    {
        if(!($living_share instanceof ViewsParams))
        {
            $error = '不是直播分享记录';
            return false;
        }
        if(!$living_share->save())
        {
            $error = '修改启用/禁用直播分享失败';
            \Yii::getLogger()->log($error.' :'.var_export($living_share->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        if(!self::DeleteLivingShareCache($error))
        {
            return false;
        }
        return true;
    }

    /**
     * 删除直播分享概率信息缓存
     * @return bool
     */
    public static function DeleteLivingShareCache()
    {
        $cache = \Yii::$app->cache->delete('living_views_share_list_info');
        if(!$cache)
        {
            \Yii::getLogger()->log('删除直播分享缓存失败',Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 将直播分享概率信息写入缓存
     * @param $error
     * @param $outInfo
     * @return bool
     */
    public static function SetLivingShareCache(&$error,&$outInfo)
    {
        $query_result = (new Query())
            ->select(['view_id','rate','beans','status','create_time'])
            ->from('mb_views_params')
            ->where('status=1')->all();
        $outInfo = $query_result;
        $query_result = json_encode($query_result);
        $cache = \Yii::$app->cache->set('living_views_share_list_info',$query_result);
        if(!$cache)
        {
            \Yii::getLogger()->log('直播分享列表缓存写入失败   query_result_list==:'.var_export($query_result,true),Logger::LEVEL_ERROR);
            $error = '直播分享信息列表获取失败';
            return false;
        }
        return true;
    }

    /**
     * 处理观众分享是否获得豆逻辑处理
     * @param $user_id
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public static function GetLivingShareRate($user_id,$nick_name,$living_id,&$outInfo,&$error,$sendData = null)
    {
        $client_active = ClientActiveUtil::GetClientActiveInfoByUserId($user_id);
        $living_info = LivingUtil::GetClientLivingInfo($living_id);

        if($living_info['living_master_id'] == $user_id)
        {
            \Yii::getLogger()->log('主播分享不获得豆',Logger::LEVEL_ERROR);
            return true;
        }

        $im_data = [
            'key_word'=>'living_views_share_im',
            'user_id' => $user_id,
            'nick_name' => $nick_name,
            'level_no' => $client_active->level_no,
            'beans' => 0,
            'other_id' => $living_info['other_id'],
            'living_id' => $living_id,
        ];

        if ($sendData && isset($sendData->extra)) {
            $im_data['extra'] = $sendData->extra;
        }

        $cache_key = 'living_share_'.date('Y-m-d').'_'.$user_id;
        $views_cache = \Yii::$app->cache->get($cache_key);
        if($views_cache !== false)       //判断分享用户是否已经获得过豆了
        {
            \Yii::getLogger()->log('今天已经分享过了   $user_id===:'.$user_id.'    $nick_name===:'.$nick_name,Logger::LEVEL_ERROR);
            self::SendViewsShareIm($im_data);
            return true;
        }
        $living_share = \Yii::$app->cache->get('living_views_share_list_info');
        if($living_share === false)
        {
            $phpLock = new PhpLock('mb_lock_living_views_share_list_info');
            $phpLock->lock();
            $living_share = \Yii::$app->cache->get('living_views_share_list_info');
            if($living_share === false)
            {
                $lucky_res = self::SetLivingShareCache($error,$outInfo);
                if(!$lucky_res)
                {
                    $phpLock->unlock();
                    return false;
                }
                $living_share = $outInfo;
            }
            else
            {
                $living_share = json_decode($living_share,true);
            }
            $phpLock->unlock();
        }
        else
        {
            $living_share = json_decode($living_share,true);
        }
        if(!isset($living_share) || empty($living_share))          //无分享概率信息
        {
            return true;
        }
        if(!self::GetPassShareRateInfo($living_share,$outInfo,$error))
        {
            self::SendViewsShareIm($im_data);
            return true;
        }
        if(!isset($outInfo['rate_list']) && empty($outInfo['rate_list']))          //无分享概率信息
        {
            self::SendViewsShareIm($im_data);
            return true;
        }

        if($outInfo['is_rate_100'] != 1)
        {
            $rate_list_key = self::GetRandRate($outInfo['rate_list'],$outInfo['float_len']);
            array_pop($outInfo['rate_list']);
            $lucky_rate_one = $outInfo['rate_list'];   //去除未获得的机率
            if(!isset($lucky_rate_one[$rate_list_key]) && empty($lucky_rate_one[$rate_list_key]))         //未获得分享豆
            {
                fwrite(STDOUT, Console::ansiFormat('未获得豆   $lucky_rate_one==='.$lucky_rate_one[$rate_list_key]."\n", [Console::FG_GREEN]));
                self::SendViewsShareIm($im_data);
                return true;
            }
        }
        else
        {
            $rate_list_key =  $outInfo['is_rate_100_key'];    //百分百机率获得
        }
        if($living_share[$rate_list_key]['beans'] <= 0)
        {
            fwrite(STDOUT, Console::ansiFormat('豆值小于0   $lucky_rate_one==='.$lucky_rate_one[$rate_list_key]."\n", [Console::FG_GREEN]));
            self::SendViewsShareIm($im_data);
            return true;
        }
//增加用户豆
        if(!self::AddBeanBalanceNum($user_id,$living_share[$rate_list_key]['beans'],$error))
        {
            \Yii::getLogger()->log('直播分享获得豆，添加失败   error===:'.$error,Logger::LEVEL_ERROR);
            \Yii::getLogger()->flush(true);
            fwrite(STDOUT, Console::ansiFormat('直播分享获得豆，添加失败   $error==='.$error[$rate_list_key]."\n", [Console::FG_GREEN]));
            self::SendViewsShareIm($im_data);
            return true;
        }
        $cache_key = 'living_share_'.date('Y-m-d').'_'.$user_id;
        $cache = \Yii::$app->cache->set($cache_key,1,3600*24*2);
        if(!$cache)
        {
            fwrite(STDOUT, Console::ansiFormat('观众分享获得豆缓存写入失败   $lucky_rate_one==='.$lucky_rate_one[$rate_list_key]."\n", [Console::FG_GREEN]));
            \Yii::getLogger()->log('观众分享获得豆缓存写入失败',Logger::LEVEL_ERROR);
            \Yii::getLogger()->flush(true);
            self::SendViewsShareIm($im_data);
            return false;
        }

        $im_data = [
            'key_word'=>'living_views_share_im',
            'user_id' => $user_id,
            'nick_name' => $nick_name,
            'level_no' => $client_active->level_no,
            'beans' => $living_share[$rate_list_key]['beans'],
            'other_id' => $living_info['other_id'],
            'living_id' => $living_id,
        ];
        if ($sendData && isset($sendData->extra)) {
            $im_data['extra'] = $sendData->extra;
        }
        self::SendViewsShareIm($im_data);
        return true;
    }

    /**
     * 向群发观众分享消息
     * @param $params
     */
    public static function SendViewsShareIm($params)
    {
        /***向群发送消息***/
        $im_data = [
            'key_word'=>'living_views_share_im',
            'user_id' => $params['user_id'],
            'nick_name' => $params['nick_name'],
            'level_no' => $params['level_no'],
            'beans' => $params['beans'],
            'other_id' => $params['other_id'],
            'living_id' => $params['living_id'],
        ];

        if (isset($params['extra'])) {
            $im_data['extra'] = $params['extra'];
        }
//        fwrite(STDOUT, Console::ansiFormat('观众分享1111   $im_data==='.var_export($im_data,true)."\n", [Console::FG_GREEN]));
        if(!JobUtil::AddImJob('tencent_im',$im_data,$error))
        {
            \Yii::getLogger()->log('观众分享获得豆im消息发送失败：'.'fail:'.$error.' date_time:'.date('Y-m-d H:i:s'),Logger::LEVEL_ERROR);
            \Yii::getLogger()->flush(true);
        }
    }

    /**
     *获取观众分享概率信息
     * @param $living_share
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public static function GetPassShareRateInfo($living_share,&$outInfo,&$error)
    {
        if(!is_array($living_share))
        {
            \Yii::getLogger()->log('观众分享概率信息参数类不是数组',Logger::LEVEL_ERROR);
            $error = '参数类型错误';
            return false;
        }
        if(empty($living_share))
        {
            \Yii::getLogger()->log('观众分享概率数据不能为空',Logger::LEVEL_ERROR);
            $error = '观众分享概率数据不能为空';
            return false;
        }
        $rate_arr = [];
        $float_len = 0;
        $flag = 0;
        $k = 0;
        foreach($living_share as $key=>$share)
        {
//            if($share['rate'] >= 100)    //机率如果为100%直接退出循环
//            {
//                $rate_arr = [];
//                $rate_arr[$key] = floatval($share['rate']);
//                $flag = 1;
//                $k = $key;
//                break;
//            }
            $rate_arr[$key] = floatval($share['rate']);
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
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }

    /**
     * 增加实际豆修改方法
     * @param $user_id
     * @param $bean_num
     */
    public static function AddBeanBalanceNum($user_id,$bean_num,&$error)
    {
        $param = [
            'bean_num'=>$bean_num
        ];
        $userBalance = BalanceUtil::GetUserBalanceByUserId($user_id);
        if(!isset($userBalance))
        {
            $error = '用户账户信息不存在';
            return false;
        }
        $transActions = [];
        $transActions[] = new ModifyBalanceByAddRealBean($userBalance,$param);

        $params = [
            'op_value'=>$bean_num,
            'operate_type'=>'23',
            'unique_id'=>UsualFunForStringHelper::CreateGUID(),
            'device_type'=>'1',
            'relate_id'=>'',
            'field'=>'bean_balance'
        ];
        $transActions[] = new CreateUserBalanceLogByTrans($userBalance,$params);
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }
}