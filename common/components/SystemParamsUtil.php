<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-16
 * Time: 下午10:17
 */

namespace common\components;
use common\models\AccountInfo;
use common\models\SystemParams;
use common\components\PhpLock;
use yii\log\Logger;

/**
 * Class 系统参数辅助类
 * @package common\components
 */
    class SystemParamsUtil
{
    private static $paramList = null;

    /**
     * 获取客户端系统的系统参数
     * @params string $codeList 多个code用英文逗号分隔
     */
    public static function GetSystemOtherParams($codeList = null)
    {
        $all_codes = ['cash_gongzhonghao','system_recharge_info','system_customer_call','sys_cash_rate','bean_num_for_danmaku','living_bean_to_experience','system_pay','heart_dis_time','living_effects_luminance_android','living_effects_beauty_android','living_effects_luminance_ios','living_effects_beauty_ios','present_golds_for_login','get_room_level_num'];
        if(empty($codeList))
        {
            $sysParams = $all_codes;
        }
        else
        {
            $sysParams = [];
            foreach($codeList as $key=>$code)
            {
                if(in_array($code,$all_codes))
                {
                    $sysParams[] = $code;
                }
            }
        }

        $paramsList = self::GetParamsByCodes($sysParams);
        $len = count($paramsList);
        $out=[];
        for($i = 0; $i <$len; $i ++ )
        {
            $item = $paramsList[$i];
            $ary=[
                'code'=>$item->code,
                'title'=>$item->title,
                'value'=>$item->value1,
            ];
            $out[] = $ary;
        }
        return $out;
    }


        /**
         * 获取无需登录的系统参数
         * @param null $codeList
         * @return array
         */
        public static function GetNoLoginSystemOtherParams($codeList = null)
        {
            $all_params_codes = ['mb_login_types'];
            if(empty($codeList))
            {
                $sysParams = ['mb_login_types'];
            }
            else
            {
                $sysParams = [];
                foreach($codeList as $key=>$code)
                {
                    if(in_array($code,$all_params_codes))
                    {
                        $sysParams[] = $code;
                    }
                }
                //\Yii::getLogger()->log('sysParams:'.var_export($sysParams,true),Logger::LEVEL_ERROR);
            }

            $paramsList = self::GetParamsByCodes($sysParams);
            $len = count($paramsList);
            $out=[];
            for($i = 0; $i <$len; $i ++ )
            {
                $item = $paramsList[$i];
                $ary=[
                    'code'=>$item->code,
                    'title'=>$item->title,
                    'value'=>$item->value1,
                ];
                $out[] = $ary;
            }
            return $out;
        }

        /**
         * 根据code批量获取记录
         * @param $codes
         * @return static[]
         */
        public static function GetParamsByCodes($codes)
        {
            return SystemParams::findAll(['code'=>$codes]);
        }

    /**
     * 获取美愿基金配置参数
     */
    public static function GetFundParams()
    {
        $code_list = ['system_stu_borrow_by_stages_rate',
            'system_social_borrow_by_stages_rate',
            'system_breach_rate',
            'system_last_breach_rate',
            'system_fund_init_money',
            'system_fund_by_stages_count',
            'system_fund_rate_for_halfdelaytimes',
            'system_fund_credite_value_for_halfdelaytimes',
            'system_fund_time_unit',
            'system_days_breach_to_lastbreach'];
            $out = [];
        $len = count($code_list);
        for($i =0; $i <$len; $i++ )
        {
            $out[$code_list[$i]] = self::GetSystemParam($code_list[$i],true);
        }
        return $out;
    }

    /**
     * 根据code获取系统参数
     * @param $code
     * @param bool $reflesh 是否刷新
     * @param bool $value_key  为null返回整个对象，否则返回字段对应的值
     * @return mixed
     */
    public static function GetSystemParam($code,$reflesh = false,$value_key='value2')
    {
        if(self::$paramList === null)
        {
            /*$lock = new PhpLock('get_single_system_param');
            $lock->lock();*/
            if(self::$paramList === null)
            {
                self::$paramList = self::GetSystemParams($reflesh);
            }
//            $lock->unlock();
        }
        if(empty($value_key))
        {
            $rst = self::$paramList[$code];
        }
        else
        {
            $rst = self::$paramList[$code][$value_key];
        }
        return $rst;
    }
    /**
     * 获取系统参数
     * @param bool $reflesh
     */
    public static function  GetSystemParams($reflesh=false)
    {
        if($reflesh)
        {
            $paramList = self::GetSysParam();
            $pStr = serialize($paramList);
            \Yii::$app->cache->set('system_param', $pStr);
        }
        else
        {
            $cnt = \Yii::$app->cache->get('system_param');
            if(!$cnt)
            {
                $lock = new PhpLock('get_system_param');
                $lock->lock();
                $cnt = \Yii::$app->cache->get('system_param');
                if(!isset($cnt))
                {
                    $paramList = self::GetSysParam();
                    $cnt = serialize($paramList);
                    \Yii::$app->cache->set('system_param', $cnt);
                }
                else
                {
                    $paramList = unserialize($cnt);
                }
                $lock->unlock();
            }
            else
            {
                $paramList = unserialize($cnt);
            }
        }
        return $paramList;
    }

    /**
     * 获取所有系统参数
     * @return array
     */
    public static function GetSysParam()
    {
        $paramList = SystemParams::find()->all();
        $paramOut = [];
        foreach($paramList as $paramOne)
        {
            $paramOut[$paramOne->code] = $paramOne->attributes;
        }
        return $paramOut;
    }

    public static function GetSysParamsByOne()
    {

    }

    /**
     * @var 单个系统参数缓存前缀
     */
    const CACHE_KEY_PREFIX     = 'system_param_alone_';
    const SYSPARAM_DEFAULT_KEY = 'value1';
    /**
     * 获取单个系统参数
     * @param string $key 系统参数code
     * @param boolean $needFlush 是否刷新，默认false
     * @return string or array or null
     */
    public static function getSystemParamWithOne($key, $needFlush = false, $isJson = true)
    {
        $cacheKey   = self::CACHE_KEY_PREFIX . $key;
        $defaultKey = self::SYSPARAM_DEFAULT_KEY;

        $value = \Yii::$app->cache->get($cacheKey);
        if ($needFlush || $value === false) {
            $row = SystemParams::find()->select($defaultKey)->where(['code' => $key])->one();
            $value = $row->$defaultKey;
            \Yii::$app->cache->set($cacheKey, $value);
        }
        return $isJson ? json_decode($value, true) : $value;
    }


} 