<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/11
 * Time: 16:35
 */

namespace frontend\business;


use yii\db\Query;

class OperateStatisUtil {

    /**
     * 获取近三天的注册人数
     * @return array
     */
    public static function GetThreeAddregnumDate()
    {
        $query = (new Query())
            ->select(['statistics_num','statistics_time'])
            ->from('mb_add_reg_num')
            ->where('statistics_type = 1 and DATE_SUB(CURDATE(), INTERVAL 2 DAY) <= date(statistics_time)')
            ->orderBy('statistics_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=2;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistics_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistics_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近三天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取近7天的注册人数
     * @return array
     */
    public static function GetSevenAddregnumDate()
    {
        $query = (new Query())
            ->select(['statistics_time','statistics_num'])
            ->from('mb_add_reg_num')
            ->where('statistics_type = 1 and DATE_SUB(CURDATE(), INTERVAL 6 DAY) <= date(statistics_time)')
            ->orderBy('statistics_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=6;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistics_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistics_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近七天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取近30天的注册人数
     * @return array
     */
    public static function GetThirtyAddregnumDate()
    {
        $query = (new Query())
            ->select(['statistics_time','statistics_num'])
            ->from('mb_add_reg_num')
            ->where('statistics_type = 1 and DATE_SUB(CURDATE(), INTERVAL 29 DAY) <= date(statistics_time)')
            ->orderBy('statistics_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=29;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistics_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistics_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近30天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取今天24小时内的注册人数
     * @return array
     */
    public static function GetOneHouseAddregnumDate()
    {
        $query = (new Query())
            ->select(['statistics_time','statistics_num', 'create_time'])
            ->from('mb_add_reg_num')
            ->where('statistics_type = 4 and to_days(statistics_time)=to_days(now())')
            ->orderBy('statistics_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=0;$i<24;$i++)
        {
            foreach($query as $vv)
            {
                if($i < 10)
                {
                    $vi = date('Y-m-d')." "."0".$i;
                }
                else
                {
                    $vi = date('Y-m-d')." ".$i;
                }
                if($vv['statistics_time'] != $vi)
                {
                    $v[$i] = 0;
                }
                else{
                    $v[$i] = (int)$vv['statistics_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '今天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取昨天24小时内的注册人数
     * @return array
     */
    public static function GetYesterdayHouseAddregnumDate()
    {
        $query = (new Query())
            ->select(['statistics_time','statistics_num'])
            ->from('mb_add_reg_num')
            ->where('TO_DAYS(NOW()) - TO_DAYS(statistics_time) = 1  and statistics_type = 4')
            ->orderBy('statistics_time asc')
            ->all();

        $v = [];
        $arr = 0;
        $date = date('Y-m-d', strtotime('-1 day'));
        for($i=0;$i<24;$i++)
        {
            foreach($query as $vv)
            {
                if($i < 10)
                {
                    $vi = $date." "."0".$i;
                }
                else
                {
                    $vi = $date." ".$i;
                }
                if($vv['statistics_time'] != $vi)
                {
                    $v[$i] = 0;
                }
                else{
                    $v[$i] = (int)$vv['statistics_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '昨天',
            'data' => $v,
            'visible' => false
        ];

        return $data;
    }



    /**
     * 获取近三天的充值数据
     * @return array
     */
    public static function GetThreeRechargeNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_recharge_money')
            ->where('statistic_type = 1 and DATE_SUB(CURDATE(), INTERVAL 2 DAY) <= date(statistic_time)')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=2;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistic_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近三天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取近七天的充值数据
     * @return array
     */
    public static function GetSevenRechargeNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_recharge_money')
            ->where('statistic_type = 1 and DATE_SUB(CURDATE(), INTERVAL 6 DAY) <= date(statistic_time)')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=6;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistic_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近七天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取近三十天的充值数据
     * @return array
     */
    public static function GetThirtyRechargeNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_recharge_money')
            ->where('statistic_type = 1 and DATE_SUB(CURDATE(), INTERVAL 29 DAY) <= date(statistic_time)')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=29;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistic_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近三十天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取今天24小时内的充值数据
     * @return array
     */
    public static function GetOneHouseRechargeNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_recharge_money')
            ->where('statistic_type = 4 and to_days(statistic_time)=to_days(now())')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=0;$i<24;$i++)
        {
            foreach($query as $vv)
            {
                if($i < 10)
                {
                    $vi = date('Y-m-d')." "."0".$i;
                }
                else
                {
                    $vi = date('Y-m-d')." ".$i;
                }
                if($vv['statistic_time'] != $vi)
                {
                    $v[$i] = 0;
                }
                else{
                    $v[$i] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '今天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取昨天24小时内的充值数据
     * @return array
     */
    public static function GetYesterdayHouseRechargeNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_recharge_money')
            ->where('TO_DAYS(NOW()) - TO_DAYS(statistic_time) = 1  and statistic_type = 4')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        $date = date('Y-m-d', strtotime('-1 day'));
        for($i=0;$i<24;$i++)
        {
            foreach($query as $vv)
            {
                if($i < 10)
                {
                    $vi = $date." "."0".$i;
                }
                else
                {
                    $vi = $date." ".$i;
                }
                if($vv['statistic_time'] != $vi)
                {
                    $v[$i] = 0;
                }
                else{
                    $v[$i] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '昨天',
            'data' => $v,
            'visible' => false
        ];

        return $data;
    }




    /**
     * 获取近三天的活跃主播人数
     * @return array
     */
    public static function GetThreeActiveAnchorNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_living_personcount')
            ->where('statistic_type = 1 and DATE_SUB(CURDATE(), INTERVAL 2 DAY) <= date(statistic_time)')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=2;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistic_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近三天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取近七天的活跃主播人数
     * @return array
     */
    public static function GetSevenActiveAnchorNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_living_personcount')
            ->where('statistic_type = 1 and DATE_SUB(CURDATE(), INTERVAL 6 DAY) <= date(statistic_time)')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=6;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistic_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近七天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取近三十天的活跃主播人数
     * @return array
     */
    public static function GetThirtyActiveAnchorNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_living_personcount')
            ->where('statistic_type = 1 and DATE_SUB(CURDATE(), INTERVAL 29 DAY) <= date(statistic_time)')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=29;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistic_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近三十天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取今天24小时内的活跃主播人数
     * @return array
     */
    public static function GetOneHouseActiveAnchorNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_living_personcount')
            ->where('statistic_type = 4 and to_days(statistic_time)=to_days(now())')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        $ss = 0;
        for($i=0;$i<24;$i++)
        {
            foreach($query as $vv)
            {
                if($i < 10)
                {
                    $vi = date('Y-m-d')." "."0".$i;
                }
                else
                {
                    $vi = date('Y-m-d')." ".$i;
                }
                if($vv['statistic_time'] != $vi)
                {
                    $ss++;
                    $v[$i] = 0;
                }
                else{
                    $v[$i] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '今天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取昨天24小时内的活跃主播人数
     * @return array
     */
    public static function GetYesterdayHouseActiveAnchorNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_living_personcount')
            ->where('TO_DAYS(NOW()) - TO_DAYS(statistic_time) = 1  and statistic_type = 4')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        $date = date('Y-m-d', strtotime("-1 day"));
        for($i=0;$i<24;$i++)
        {
            foreach($query as $vv)
            {
                if($i < 10)
                {
                    $vi = $date." "."0".$i;
                }
                else
                {
                    $vi = $date." ".$i;
                }
                if($vv['statistic_time'] != $vi)
                {
                    $v[$i] = 0;
                }
                else{
                    $v[$i] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '昨天',
            'data' => $v,
            'visible' => false
        ];

        return $data;
    }



    /**
     * 获取近三天的活跃用户人数
     * @return array
     */
    public static function GetThreeActiveUserNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_active_client')
            ->where('statistic_type = 1 and DATE_SUB(CURDATE(), INTERVAL 2 DAY) <= date(statistic_time)')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=2;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistic_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近三天',
            'data' => $v

        ];

        return $data;
    }

    /**
     * 获取近七天的活跃用户人数
     * @return array
     */
    public static function GetSevenActiveUserNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_active_client')
            ->where('statistic_type = 1 and DATE_SUB(CURDATE(), INTERVAL 6 DAY) <= date(statistic_time)')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=6;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistic_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近七天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取近三十天的活跃用户人数
     * @return array
     */
    public static function GetThirtyActiveUserNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_active_client')
            ->where('statistic_type = 1 and DATE_SUB(CURDATE(), INTERVAL 29 DAY) <= date(statistic_time)')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        for($i=29;$i>=0;$i--)
        {
            foreach($query as $vv)
            {

                if($vv['statistic_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$arr] = 0;
                }
                else{
                    $v[$arr] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '近三十天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取今天24小时内的活跃用户人数
     * @return array
     */
    public static function GetOneHouseActiveUserNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_active_client')
            ->where('statistic_type = 4 and to_days(statistic_time)=to_days(now())')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        $ss = 0;
        for($i=0;$i<24;$i++)
        {
            foreach($query as $vv)
            {
                if($i < 10)
                {
                    $vi = date('Y-m-d')." "."0".$i;
                }
                else
                {
                    $vi = date('Y-m-d')." ".$i;
                }
                if($vv['statistic_time'] != $vi)
                {
                    $ss++;
                    $v[$i] = 0;
                }
                else{
                    $v[$i] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '今天',
            'data' => $v
        ];

        return $data;
    }

    /**
     * 获取昨天24小时内的活跃用户人数
     * @return array
     */
    public static function GetYesterdayHouseActiveUserNumDate()
    {
        $query = (new Query())
            ->select(['statistic_time','statistic_num'])
            ->from('mb_statistic_active_client')
            ->where('TO_DAYS(NOW()) - TO_DAYS(statistic_time) = 1  and statistic_type = 4')
            ->orderBy('statistic_time asc')
            ->all();

        $v = [];
        $arr = 0;
        $date = date('Y-m-d', strtotime("-1 day"));
        for($i=0;$i<24;$i++)
        {
            foreach($query as $vv)
            {
                if($i < 10)
                {
                    $vi = $date." "."0".$i;
                }
                else
                {
                    $vi = $date." ".$i;
                }
                if($vv['statistic_time'] != $vi)
                {
                    $v[$i] = 0;
                }
                else{
                    $v[$i] = (int)$vv['statistic_num'];
                    break;
                }
            }
            $arr++;
        }

        $data = [
            'name' => '昨天',
            'data' => $v,
            'visible' => false
        ];

        return $data;
    }
}