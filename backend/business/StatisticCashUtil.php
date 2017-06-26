<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/27
 * Time: 17:00
 */

namespace backend\business;


use yii\db\Query;
use yii\log\Logger;

class StatisticCashUtil
{
    /**
     *  统计当天提现金额
     */
    public static function StatisticDayNum()
    {
        $condition = 'status=3 and DATE_FORMAT(check_time,\'%Y-%m-%d\') = DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
        $query = (new Query())
            ->select(['ifnull(SUM(real_cash_money),0) as statistic_num'])
            ->from('mb_ticket_to_cash')
            ->where($condition)
            ->one();
        $query['statistic_time'] = date('Y-m-d');
        return $query;
    }

    /**
     * 统计当周提现金额
     */
    public static function StatisticWeekNum(){
        $first_week_date = date('Y-m-d',(time()-((date('w')==0?7:date('w'))-1)*24*3600));
        $last_week_date = date('Y-m-d',(time()+(7-(date('w')==0?7:date('w')))*24*3600));

        $condition = 'statistic_type=1 and DATE_FORMAT(statistic_time,\'%Y-%m-%d\') between "'.$first_week_date.'" and "'.$last_week_date.'"';
        $query = (new Query())
            ->select(['ifnull(SUM(statistic_num),0) as statistic_num'])
            ->from('mb_statistic_cash')
            ->where($condition)
            ->one();
        $query['statistic_time'] = date('Y',(time()-((date('w')==0?7:date('w'))-1)*24*3600)).'-'.date('W');
        return $query;
    }

    /**
     * 统计当月提现金额
     */
    public static function StatisticMonthNum(){
//        $first_month_date = date('Y-m-d',strtotime(date('Y-m', time()).'-01 00:00:00'));
//        $last_month_date = date('Y-m-d',strtotime(date('Y-m', time()).'-'.date('t', time()).' 00:00:00'));

        $first_month_date = date('Y-m-01');
        $last_month_date = date('Y-m-t');
        $condition = 'statistic_type=1 and statistic_time between "'.$first_month_date.'" and "'.$last_month_date.'"';
        $query = (new Query())
            ->select(['ifnull(SUM(statistic_num),0) as statistic_num'])
            ->from('mb_statistic_cash')
            ->where($condition)
            ->one();
        $query['statistic_time'] = date('Y-m');
        return $query;

    }

    /**
     *  统计昨天提现金额
     */
    public static function StatisticYesterdayNum()
    {
        $condition = 'status=3 and DATE_FORMAT(check_time,\'%Y-%m-%d\') = DATE_FORMAT(date_sub(NOW(),interval 1 day),\'%Y-%m-%d\')';
        $query = (new Query())
            ->select(['ifnull(SUM(real_cash_money),0) as statistic_num'])
            ->from('mb_ticket_to_cash')
            ->where($condition)
            ->one();
        $query['statistic_time'] = date('Y-m-d',strtotime('-1 day', time()));
        return $query;
    }

    /**
     * 统计上周提现金额
     */
    public static function StatisticLastWeekNum(){

        $first_week_date = date('Y-m-d',strtotime('-6 day',strtotime('-1 sunday',time())));
        $last_week_date = date('Y-m-d',strtotime('-1 sunday', time()));
        $condition = 'statistic_type=1 and DATE_FORMAT(statistic_time,\'%Y-%m-%d\') between "'.$first_week_date.'" and "'.$last_week_date.'"';
        $query = (new Query())
            ->select(['ifnull(SUM(statistic_num),0) as statistic_num'])
            ->from('mb_statistic_cash')
            ->where($condition)
            ->one();
        $query['statistic_time'] = date('Y-W',strtotime($first_week_date));
        return $query;
    }


    /**
     * 统计上月提现金额
     */
    public static function StatisticLastMonthNum(){
//        $first_month_date = date('Y-m-d',strtotime('-1 month', strtotime(date('Y-m', time()).'-01 00:00:00')));
//        $last_month_date = date('Y-m-d',strtotime(date('Y-m', time()).'-01 00:00:00')-86400);

        $first_month_date = date('Y-m-01',strtotime('-1 month',strtotime(date('Y-m-01'))));
        $last_month_date = date('Y-m-t',strtotime('-1 month',time()));
        $condition = 'statistic_type=1 and statistic_time between "'.$first_month_date.'" and "'.$last_month_date.'"';
        $query = (new Query())
            ->select(['ifnull(SUM(statistic_num),0) as statistic_num'])
            ->from('mb_statistic_cash')
            ->where($condition)
            ->one();
        $query['statistic_time'] = date('Y-m',strtotime('-1 month', strtotime(date('Y-m', time()).'-01 00:00:00')));
        return $query;

    }

    /**
     * 统计一段时间内每天的提现 默认统计前6天
     * @param string $firstday
     * @param string $lastday
     * @return array
     */
    public static function StatisticFirstToLastDayNum($firstday='',$lastday='')
    {
        if(empty($firstday)){
            $firstday = date('Y-m-d',strtotime('-6 day',time()));
        }
        if(empty($lastday)){
            $lastday = date('Y-m-d',strtotime('-1 day',time()));
        }
        $condition = 'statistic_type=1 and DATE_FORMAT(statistic_time,\'%Y-%m-%d\') between "'.$firstday.'" and "'.$lastday.'"';
        $query = (new Query())
            ->select(['ifnull(SUM(statistic_num),0) as statistic_num','statistic_time'])
            ->from('mb_statistic_cash')
            ->where($condition)
            ->groupBy('statistic_time')
            ->all();
        $get_date = [];
        foreach($query as $v)
        {
            $get_date[$v['statistic_time']] = $v['statistic_num'];
        }
//        $first_day = date('z',strtotime($lastday))+1;
//        $last_day = date('z',strtotime($firstday))+1;
        $m_day_len = 5;//$first_day-$last_day;

        $result_query = [];
        for( $i=0; $i <= $m_day_len; $i++ )
        {
            $day_date = date('Y-m-d',strtotime($i.' day',strtotime($firstday)));
            if(isset($get_date[$day_date]))
            {
                $result_query[] = [
                    'statistic_num' => $get_date[$day_date],
                    'statistic_time' => $day_date
                ];
                continue;
            }
            $result_query[] = [
                'statistic_num' =>0,
                'statistic_time' => $day_date
            ];
        }
        return $result_query;
    }


    /**
     * 统计一段周数内的的提现 默认前6周
     * @return array|bool
     */
    public static function StatisticFirstToLastWeekNum($system_first_week_date='',$system_last_week_date=''){
        if(empty($system_first_week_date))
        {
            $system_first_week_date = date('Y-m-d',strtotime('-6 week',strtotime(date('Y-m-d',strtotime('-1 monday', time())))));
        }
        if(empty($system_last_week_date))
        {
            $system_last_week_date = date('Y-m-d',strtotime('-6 day',strtotime('-1 sunday',time())));
        }
        $first_week_date = date('Y-W',strtotime($system_first_week_date));
        $last_week_date = date('Y-W',strtotime($system_last_week_date));
        $condition = 'statistic_type=2 and statistic_time between "'.$first_week_date.'" and "'.$last_week_date.'"';
        $query = (new Query())
            ->select(['ifnull(SUM(statistic_num),0) as statistic_num','statistic_time'])
            ->from('mb_statistic_cash')
            ->where($condition)
            ->groupBy('statistic_time')
            ->all();
        $get_date = [];
        foreach($query as $v){
            $get_date[$v['statistic_time']] = $v['statistic_num'];
        }
//        $first_week = date('W',strtotime($system_first_week_date));
//        $last_week = date('W',strtotime($system_last_week_date));
        $week_len = 5;//$last_week-$first_week;

        $result_query = [];
        for($i=0;$i<=$week_len;$i++){
            $week_date = date('Y-W',strtotime($i.' week',strtotime($system_first_week_date)));
            if(isset($get_date[$week_date])){
                $result_query[] = [
                    'statistic_num' => $get_date[$week_date],
                    'statistic_time' => $week_date
                ];
                continue;
            }
            $result_query[] = [
                'statistic_num' =>0,
                'statistic_time' => $week_date
            ];
        }
        return $result_query;

    }


    /**
     * 统计一段时间内每月的提现  默认前6月
     * @return array|bool
     */
    public static function StatisticFirstToLastMonthNum($first_month_date='',$last_month_date=''){
        if(empty($first_month_date))
        {
            $first_month_date = date('Y-m-d',strtotime('-5 month',strtotime(date('Y-m-01',strtotime('-1 month',strtotime(date('Y-m-01')))))));
        }
        if(empty($last_month_date))
        {
            $last_month_date = date('Y-m-d',strtotime('-1 month',time()));
        }

        $first_date = date('Y-m',strtotime($first_month_date));
        $last_date = date('Y-m',strtotime($last_month_date));

        $condition = 'statistic_type=3 and statistic_time between "'.$first_date.'" and "'.$last_date.'"';
        $query = (new Query())
            ->select(['ifnull(SUM(statistic_num),0) as statistic_num','statistic_time'])
            ->from('mb_statistic_cash')
            ->where($condition)
            ->groupBy('statistic_time')
            ->all();

        $get_date = [];
        foreach($query as $v){
            $get_date[$v['statistic_time']] = $v['statistic_num'];
        }

        $first_month_date1 = explode('-',$first_month_date);
        $last_month_date2 = explode('-',$last_month_date);

        $month_len = abs(abs($first_month_date1[0] - $last_month_date2[0])*12 - abs($first_month_date1[1] - $last_month_date2[1]));
        $result_query = [];
        for($i=0;$i<=$month_len;$i++){
            $month_date = date('Y-m',strtotime($i.' month',strtotime($first_date)));
            if(isset($get_date[$month_date])){
                $result_query[] = [
                    'statistic_num' => $get_date[$month_date],
                    'statistic_time' => $month_date
                ];
                continue;
            }
            $result_query[] = [
                'statistic_num' =>0,
                'statistic_time' => $month_date
            ];
        }
        return $result_query;
    }


    /**
     * 组装insert语句一次性插入
     * @param $arr 一维数据｜二维数组
     * @param int $type
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function InsertData($arr,$type=1,&$error){
        if(!is_array($arr)){
            $error = '参数非数组';
            return false;
        }
        $inser_date = '';
        $flag = 1; //一维数组
        foreach($arr as $key=>$v){
            if(is_array($v)){
                $flag = 2; //二维数组
            }
            break;
        }

        if($flag == 1){
            $inser_date .= '('.$type.',"'.$arr['statistic_time'].'",'.$arr['statistic_num'].'),';
        }

        if($flag == 2){
            foreach($arr as $key=>$v){
                $inser_date .= '('.$type.',"'.$v['statistic_time'].'",'.$v['statistic_num'].'),';
            }
        }

        $inser_date = rtrim($inser_date,',');
        $sql = 'insert into mb_statistic_cash (statistic_type,statistic_time,statistic_num) values'.$inser_date.';';

        $result = \Yii::$app->db->createCommand($sql)->execute();
        if($result <= 0){
            \Yii::getLogger()->log('sql === '.\Yii::$app->db->createCommand($sql)->rawSql,Logger::LEVEL_ERROR);
            $error = '提现金额统计失败';
            return false;
        }
        return true;
    }


    /**
     * 判断日期是否已经统计过
     * @param $type
     * @param $statistic_time
     * @param $error
     * @return bool
     */
    public static function CheckDateIsExist($type,$statistic_time,&$error){
        $query = (new Query())
            ->select(['record_id'])
            ->from('mb_statistic_cash')
            ->where('statistic_type=:type and statistic_time = :stime',[
                ':type' => $type,
                ':stime' => $statistic_time
            ])
            ->one();
        if(!empty($query['record_id'])){
            $error = '数据已经统计过了';
            return false;
        }
        return true;
    }

}