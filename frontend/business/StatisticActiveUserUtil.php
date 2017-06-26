<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/6/12
 * Time: 13:46
 */

namespace frontend\business;


use common\models\StatisticActiveUser;
use yii\db\Query;
use yii\log\Logger;

class StatisticActiveUserUtil
{
    /**
     * 根据时间和类型获取记录
     * @param $time 日期或月份，日活日期格式 2016-06-07 月活日期格式 2016-06
     * @param $type 1 日活  2 月活
     */
    public static function GetRecordByTimeAndType($time,$type)
    {
       return StatisticActiveUser::findOne(['statistic_type'=>$type,'statistic_time'=>$time]);
    }

    /**
     * 日活统计
     */
    public static function DayActive(&$error)
    {
        $time = strtotime('-1 days');
        $date = date('Y-m-d',$time);
        $record = StatisticActiveUserUtil::GetRecordByTimeAndType($date,1);
        if(isset($record))
        {
            //已经统计过，返回true
            return true;
        }
        $start_time = date('Y-m-d 00:00:00',$time);
        $end_time = date('Y-m-d 23:59:59',$time);
        $sql = 'select count(DISTINCT unique_no) from mb_api_log where fun_id=\'get_hot_living\' and create_time BETWEEN :t1 and :t2';
        $num = \Yii::$app->db->createCommand($sql,[':t1'=>$start_time,':t2'=>$end_time])->queryScalar();
        if($num === false || $num === null)
        {
            $sql = \Yii::$app->db->createCommand($sql,[':t1'=>$start_time,':t2'=>$end_time])->rawSql;
            \Yii::getLogger()->log('日活统计无数据：'.$sql,Logger::LEVEL_ERROR);
            return true;
        }
        $record = new StatisticActiveUser();
        $record->statistic_time = $date;
        $record->statistic_type = '1';
        $record->user_num = $num;
        if(!$record->save())
        {
            $error = '保存日统计记录失败';
            \Yii::getLogger()->log('保存日活统计记录失败：'.var_export($record->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 月活统计
     */
    public static function MonthActive(&$error)
    {
        $time = date('Y-m-01');
        $lastMonthTime = strtotime($time.' -1 month');//date('Y-m',strtotime('-1 month'));
        $lastMonth = date('Y-m',$lastMonthTime);
        $record = StatisticActiveUserUtil::GetRecordByTimeAndType($lastMonth,2);
        if(isset($record))
        {
            //已经统计过，返回true
            return true;
        }
        $start_time = date('Y-m-01',$lastMonthTime);
        $end_time = date('Y-m-t',$lastMonthTime);
        $sql ='select count(DISTINCT unique_no) from mb_api_log where fun_id=\'get_hot_living\' and create_time BETWEEN :t1 and :t2 ';
        $num = \Yii::$app->db->createCommand($sql,[':t1'=>$start_time,':t2'=>$end_time])->queryScalar();
        if($num === false || $num === null)
        {
            $sql = \Yii::$app->db->createCommand($sql,[':t1'=>$start_time,':t2'=>$end_time])->rawSql;
            \Yii::getLogger()->log('月活统计无数据：'.$sql,Logger::LEVEL_ERROR);
            return true;
        }
        $record = new StatisticActiveUser();
        $record->statistic_time = $lastMonth;
        $record->statistic_type = '2';
        $record->user_num = $num;
        if(!$record->save())
        {
            $error = '保存月统计记录失败';
            \Yii::getLogger()->log('保存月活统计记录失败：'.var_export($record->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        //date('Y-m-t 23:59:59')
        return true;
    }


    /**
     *  统计当天日活
     */
    public static function StatisticDayNum()
    {
        $condition = 'fun_id=\'get_hot_living\' and DATE_FORMAT(create_time,\'%Y-%m-%d\') = DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
        $query = (new Query())
            ->select(['count(DISTINCT unique_no) as statistic_num'])
            ->from('mb_api_log')
            ->where($condition)
            ->one();
        $query['statistic_time'] = date('Y-m-d');
        return $query;
    }

    /**
     * 统计当月打赏人数
     */
    public static function StatisticMonthNum(){
        $first_month_date = date('Y-m-01');
        $last_month_date = date('Y-m-t');
        $condition = 'fun_id=\'get_hot_living\' and DATE_FORMAT(create_time,\'%Y-%m-%d\') between "'.$first_month_date.'" and "'.$last_month_date.'"';
        $query = (new Query())
            ->select(['count(DISTINCT unique_no) as statistic_num'])
            ->from('mb_api_log')
            ->where($condition)
            ->one();
        $query['statistic_time'] = date('Y-m');
        return $query;
    }


    /**
     * 统计每天的获得票数最多的主播
     */
    public static function DailyLivingMaster(){
        $condition = ' statistic_type = 2 AND statistic_time = date_sub(curdate(),interval 1 day)';
        $query = (new Query())
            ->select(['user_id','statistic_time','real_tickets_num'])
            ->from('mb_statistic_living_master')
            ->where($condition)
            ->groupBy('user_id')
            ->orderBy('real_tickets_num desc')
            ->all();

        return $query;
    }


    /**
     * 取出每周的前30个获得票数最多的主播,分别得到 昵称，头像，性别，票数（魅力值）,是否关注
     */
    public static function WeekLivingMaster(){
        $week = date('Y-W', strtotime('0 week', time()));
        $query = (new Query())
            ->select(['mtt.livingmaster_id as user_id','mtt.real_ticket_num as charisma','IFNULL(nullif(mc.middle_pic,\'\'),mc.pic) as pic','mc.nick_name as name','mc.sex'])
            ->from('mb_time_livingmaster_ticketcount mtt')
            ->innerJoin('mb_client mc','mc.client_id = mtt.livingmaster_id')
            ->where('hot_type = 2 AND  mc.is_inner = 1 AND statistic_date = :week',[':week' => $week])
            ->orderBy('real_ticket_num desc')
            ->limit(30)
            ->all();

        return $query;
    }

    /**
     * 取出每周的前30个获得票数最多的主播,是否关注
     */
    public static function WeekLivingMasterAttention($client_id){
        $week = date('Y-W', strtotime('0 week', time()));
        $query = (new Query())
            ->select(['ifnull(ma.user_id,0) as is_attention'])
            ->from('mb_time_livingmaster_ticketcount mtt')
            ->innerJoin('mb_client mc','mc.client_id = mtt.livingmaster_id')
            ->leftJoin('mb_attention ma','ma.friend_user_id = mtt.livingmaster_id and ma.user_id = :user_id',[':user_id' => $client_id])
            ->where('hot_type = 2 AND mc.is_inner = 1 AND statistic_date = :week',[':week' => $week])
            ->orderBy('real_ticket_num desc')
            ->limit(30)
            ->all();

        return $query;
    }


    /**
     * 取出每周的前4个获得票数最多的主播,分别得到 昵称，头像，性别，票数（魅力值）
     */
    public static function  WeekFiveLivingMaster(){
        $week = date('Y-W', strtotime('0 week', time()));
        $query = (new Query())
            ->select(['mtt.livingmaster_id as user_id','mtt.real_ticket_num as charisma','IFNULL(nullif(mc.middle_pic,\'\'),mc.pic) as pic','mc.nick_name as name','mc.sex'])
            ->from('mb_time_livingmaster_ticketcount mtt')
            ->innerJoin('mb_client mc','mc.client_id = mtt.livingmaster_id')
            ->where('hot_type = 2  AND  mc.is_inner = 1 AND statistic_date = "'.$week.'"')
            ->orderBy('real_ticket_num desc')
            ->limit(4)
            ->all();
        return $query;
    }

    /**
     * 取出每周的前4个获得票数最多的主播,分别得到 昵称，头像，性别，票数（魅力值）
     * @param $livingType [1,2]或 1,或 [1,2,3]
     * @return array
     */
    public static function WeekFiveLivingMasterByLivingType($livingType){
        $week = date('Y-W', strtotime('0 week', time()));
        $query = (new Query())
            ->select(['mtt.livingmaster_id as user_id','mtt.real_ticket_num as charisma','IFNULL(nullif(mc.middle_pic,\'\'),mc.pic) as pic','mc.nick_name as name','mc.sex'])
            ->from('mb_time_livingmaster_ticketcount mtt')
            ->innerJoin('mb_client mc','mc.client_id = mtt.livingmaster_id')
            ->innerJoin('mb_living l','mc.client_id = l.living_master_id')
            ->where('hot_type = 2  AND  mc.is_inner = 1 AND statistic_date = "'.$week.'"')
            ->andFilterWhere(['l.living_type'=>$livingType])
            ->orderBy('real_ticket_num desc')
            ->limit(4)
           ->all();
        return $query;
    }


    /**
     * 取出前30个总共获得票数最多的主播,分别得到 昵称，头像，性别，票数（魅力值）
     */
    public static function TotalLivingMaster(){
        $query = (new Query())
            ->select(['mb.user_id','ticket_count_sum as charisma','IFNULL(nullif(mc.icon_pic,\'\'),mc.pic) as pic','mc.nick_name as name','mc.sex'])
            ->from('mb_balance mb')
            ->innerJoin('mb_client mc','mc.client_id = mb.user_id')
            ->where(['mc.is_inner'=>1])
            ->orderBy('ticket_count_sum desc')
            ->limit(30)
            ->all();
        return $query;
    }

    /**
     * 取出获得票数总数最多的主播 关注属性
     * @param $client_id
     * @return array
     */
    public static function TotalLivingMasterAttention($client_id){
        $query = (new Query())
            ->select(['ifnull(ma.user_id,0) as is_attention'])
            ->from('mb_balance mb')
            ->innerJoin('mb_client mc','mc.client_id = mb.user_id')
            ->leftJoin('mb_attention ma','ma.friend_user_id = mb.user_id and ma.user_id = :user_id',[':user_id' => $client_id])
            ->orderBy('ticket_count_sum desc')
            ->where(['mc.is_inner'=>1])
            ->limit(30)
            ->all();
        return $query;
    }

    /**
     * 取出前4个总共获得票数最多的主播,分别得到 昵称，头像，性别，票数（魅力值）
     */
    public static function TotalFiveLivingMaster(){
        $query = (new Query())
            ->select(['mb.user_id','ticket_count_sum as charisma','IFNULL(mc.middle_pic,mc.pic) as pic','mc.nick_name as name','mc.sex'])
            ->from('mb_balance mb')
            ->innerJoin('mb_client mc','mc.client_id = mb.user_id')
            ->where(['mc.is_inner'=>1])
            ->orderBy('ticket_count_sum desc')
            ->limit(4)
            ->all();
        return $query;
    }

    /**
     * 取出前4个总共获得票数最多的主播,分别得到 昵称，头像，性别，票数（魅力值）
     * @param $livingType  [1,2]或1 或[1,2,3]
     * @return array
     */
    public static function TotalFiveLivingMasterByLivingType($livingType){
        $query = (new Query())
            ->select(['mb.user_id','ticket_count_sum as charisma','IFNULL(mc.middle_pic,mc.pic) as pic','mc.nick_name as name','mc.sex'])
            ->from('mb_balance mb')
            ->innerJoin('mb_client mc','mc.client_id = mb.user_id')
            ->innerJoin('mb_living l','l.living_master_id = mc.client_id')
            ->andFilterWhere(['mc.is_inner'=>1])
            ->andFilterWhere(['l.living_type'=>$livingType])
            ->orderBy('ticket_count_sum desc')
            ->limit(4)
            ->all();
        return $query;
    }


    /**
     * 获取主播自己本周的票数
     */
    public static function SelfCharmWeek($client_id){
        $week = date('Y-W', strtotime('0 week', time()));
        $query = (new Query())
            ->select(['mtt.real_ticket_num as charm_week'])
            ->from('mb_time_livingmaster_ticketcount mtt')
            ->where('hot_type = 2 AND statistic_date = :week and livingmaster_id = :id',[':week' => $week,':id' => $client_id])
            ->one();

        return $query;
    }

    /**
     * 获取主播自己的总票数
     */
    public static function SelfCharmTotal($client_id){
        $query = (new Query())
            ->select(['ticket_count_sum as charm_total','send_ticket_count as tyrant_total','IFNULL(mc.icon_pic,mc.pic) as pic','mc.unique_no'])
            ->from('mb_balance mb')
            ->innerJoin('mb_client mc','mc.client_id = mb.user_id')
            ->where(' user_id = :id',[':id' => $client_id])
            ->one();
        return $query;
    }


    /**
     * 将统计的每天的前50个获得票数最多的主播插入到表'mb_statistic_daily_living_master'
     */
    public static function InsertDailyLivingMaster($arr,&$error){
        if(!is_array($arr))
        {
            $error = '参数非数组';
            return false;
        }

        if(empty($arr))
        {
            $error = '参数为空';
            return false;
        }

        $sql = 'insert into mb_statistic_daily_living_master (real_tickets_date,living_master_id,real_tickets_num) values ';

        $params = [];
        $i = 1;
        $max = count($arr);
        foreach($arr as $v)
        {
            $params[':tim'.$i] = $v['statistic_time'];
            $params[':uid'.$i] = $v['user_id'];
            $params[':num'.$i] = $v['real_tickets_num'];
            $sql .= sprintf('(:tim%d,:uid%d,:num%d)',$i,$i,$i);
            if($i === $max)
            {
                $sql .= ';';
            }
            else
            {
                $sql .= ',';
            }
            $i++;
        }


        $result = \Yii::$app->db->createCommand($sql,$params)->execute();
        if($result <= 0)
        {
            \Yii::getLogger()->log('sql === '.\Yii::$app->db->createCommand($sql)->rawSql,Logger::LEVEL_ERROR);
            $error = '提现当日收到的票数榜失败';
            return false;
        }
        return true;
    }


    /**
     * 统计每天的前50个充值最多的用户
     */
    public static function DailyRecharge(){
        $day = date('Y-m-d',time()-24*60*60);
        $query = (new Query())
            ->select(['create_time','user_id','SUM(pay_money) as pay_money'])
            ->from('mb_recharge')
            ->where('status_result = 2 and create_time BETWEEN :start_t AND :end_t',[':start_t' => $day." 00:00:00",':end_t' => $day." 23:59:59"])
            ->groupBy('user_id')
            ->orderBy('pay_money desc')
            ->all();
        return $query;
    }

    /**
     * 将统计的每天的前50个充值最多的用户插入到表'mb_statistic_daily_recharge'
     */
    public static function InsertDailyRecharge($arr,&$error){
        if(!is_array($arr)){
            $error = '参数非数组';
            return false;
        }
        if(empty($arr)){
            $error = '参数为空';
            return false;
        }

        $sql = 'insert into mb_statistic_daily_recharge (recharge_date,user_id,recharge_amount) values';

        $params = [];
        $i = 1;
        $max = count($arr);
        foreach($arr as $v)
        {
            $params[':tim'.$i] = $v['create_time'];
            $params[':uid'.$i] = $v['user_id'];
            $params[':num'.$i] = $v['pay_money'];
            $sql .= sprintf('(:tim%d,:uid%d,:num%d)',$i,$i,$i);
            if($i === $max)
            {
                $sql .= ';';
            }
            else
            {
                $sql .= ',';
            }
            $i++;
        }

        $result = \Yii::$app->db->createCommand($sql,$params)->execute();
        if($result <= 0){
            \Yii::getLogger()->log('sql === '.\Yii::$app->db->createCommand($sql)->rawSql,Logger::LEVEL_ERROR);
            $error = '充值的排行榜获取失败';
            return false;
        }
        return true;
    }


    /**
     * 统计每天的前50个送礼物最多的用户
     */
    public static function DailyGift(){
        $day = date('Y-m-d',time()-24*60*60);
        $query = (new Query())
            ->select(['create_time','reward_user_id','SUM(gift_value) as gift_value'])
            ->from('mb_reward')
            ->where('status = 1 and create_time BETWEEN :start_t AND :end_t',[':start_t' => $day." 00:00:00",':end_t' => $day." 23:59:59"])
            ->groupBy('reward_user_id')
            ->orderBy('gift_value desc')
            ->all();

        return $query;
    }


    /**
     * 取出每周的前30个送礼物最多的用户
     */
    public static function WeekGift(){
        $week = date('Y-W', strtotime('0 week', time()));

        $query = (new Query())
            ->select(['br.reward_user_id as user_id','ticket_num as wealth','IFNULL(nullif(mc.icon_pic,\'\'),mc.pic) as pic','mc.nick_name as name','mc.sex'])
            ->from('mb_sum_week_reward_tickets br')
            ->innerJoin('mb_client mc','mc.client_id = br.reward_user_id')
            ->where('mc.is_inner = 1 AND date_week = :week',[':week' => $week])
            ->orderBy('ticket_num desc')
            ->limit(30)
            ->all();

        return $query;
    }


    /**
     * 关注
     */
    public static function WeekGiftAttention($client_id){
        $week = date('Y-W', strtotime('0 week', time()));

        $query = (new Query())
            ->select(['ifnull(ma.user_id,0) as is_attention'])
            ->from('mb_sum_week_reward_tickets br')
            ->innerJoin('mb_client mc','mc.client_id = br.reward_user_id')
            ->leftJoin('mb_attention ma','ma.friend_user_id = br.reward_user_id and ma.user_id = :user_id',[':user_id' => $client_id])
            ->where('date_week = :week AND mc.is_inner = 1',[':week' => $week])
            ->orderBy('ticket_num desc')
            ->limit(30)
            ->all();
        return $query;
    }


    /**
     * 取出前30个总共送礼物最多的用户
     */
    public static function TotalGift(){
        $query = (new Query())
            ->select(['mb.user_id','send_ticket_count as wealth','IFNULL(nullif(mc.icon_pic,\'\'),mc.pic) as pic','mc.nick_name as name','mc.sex'])
            ->from('mb_balance mb')
            ->innerJoin('mb_client mc','mc.client_id = mb.user_id')
            ->where(['mc.is_inner'=>1])
            ->orderBy('send_ticket_count desc')
            ->limit(30)
            ->all();

        return $query;
    }


    /**
     * 关注
     */
    public static function TotalGiftAttention($client_id){
        $query = (new Query())
            ->select(['ifnull(ma.user_id,0) as is_attention'])
            ->from('mb_balance mb')
            ->innerJoin('mb_client mc','mc.client_id = mb.user_id')
            ->leftJoin('mb_attention ma','ma.friend_user_id = mb.user_id and ma.user_id = :user_id',[':user_id' => $client_id])
            ->orderBy('send_ticket_count desc')
            ->where(['mc.is_inner'=>1])
            ->limit(30)
            ->all();

        return $query;
    }


    /**
     * 取出主播自己本周送出的礼物
     */
    public static function SelfTyrantWeek($client_id){
        $week = date('Y-W', strtotime('0 week', time()));

        $query = (new Query())
            ->select(['ticket_num as tyrant_week'])
            ->from('mb_sum_week_reward_tickets')
            ->where('date_week = :week and reward_user_id = :id',[':week' => $week,':id' => $client_id])
            ->one();

        return $query;
    }



    /**
     * 将统计的每天的前50个送礼物最多的用户插入到表'mb_statistic_daily_send_gift'
     */
    public static function InsertDailyGift($arr,&$error){
        if(!is_array($arr)){
            $error = '参数非数组';
            return false;
        }
        if(empty($arr)){
            $error = '送礼物用户为空';
            return false;
        }

        $sql = 'insert into mb_statistic_daily_send_gift (send_gift_date,living_master_id,send_gift_num) values';

        $params = [];
        $i = 1;
        $max = count($arr);
        foreach($arr as $v){
            $params[':tim'.$i] = $v['create_time'];
            $params[':uid'.$i] = $v['reward_user_id'];
            $params[':num'.$i] = $v['gift_value'];
            $sql .= sprintf('(:tim%d,:uid%d,:num%d)',$i,$i,$i);
            if($i === $max)
            {
                $sql .= ';';
            }
            else
            {
                $sql .= ',';
            }
            $i++;
        }

        $result = \Yii::$app->db->createCommand($sql,$params)->execute();
        if($result <= 0){
            \Yii::getLogger()->log('sql === '.\Yii::$app->db->createCommand($sql)->rawSql,Logger::LEVEL_ERROR);
            $error = '礼物排行榜获取失败';
            return false;
        }
        return true;
    }


    /**
     * 统计一段时间内每天的日活 默认统计6天
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
            ->select(['ifnull(SUM(user_num),0) as statistic_num','statistic_time'])
            ->from('mb_statistic_active_user')
            ->where($condition)
            ->groupBy('statistic_time')
            ->all();
        $get_date = [];
        foreach($query as $v){
            $get_date[$v['statistic_time']] = $v['statistic_num'];
        }
        $first_day = date('z',strtotime($lastday))+1;
        $last_day = date('z',strtotime($firstday))+1;
        $m_day_len = $first_day-$last_day;

        $result_query = [];
        for($i=0;$i<=$m_day_len;$i++){
            $day_date = date('Y-m-d',strtotime($i.' day',strtotime($firstday)));
            if(isset($get_date[$day_date])){
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
     * 统计一段时间内每月的日活   默认前6月
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
            ->select(['ifnull(SUM(user_num),0) as statistic_num','statistic_time'])
            ->from('mb_statistic_active_user')
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
     * 获取用户关注的人
     * @param $user_id
     * @return array
     */
    public static function GetUserAttention( $user_id ){
        $query = (new Query())
            ->select(['friend_user_id'])
            ->from('mb_attention ')
            ->where(['user_id'=>$user_id])
            ->all();
        return $query;
    }
} 