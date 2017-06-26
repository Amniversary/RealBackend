<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/6/12
 * Time: 13:46
 */

namespace frontend\business;


use common\models\StatisticFamilyTicket;
use yii\db\Query;
use yii\log\Logger;

class StatisticFamilyTicketUtil
{
    /**
     * 判断日期是否已经统计过
     * @param $time
     * @return null|static
     */
    public static function IsStatisticFamilyTicket($time,&$error)
    {
        $out = StatisticFamilyTicket::findOne(['create_time'=>$time]);
        if(!empty($out->family_id))
        {
            $error = '数据已经统计过了';
            return false;
        }
        return true;
    }
    /**
     * 统计家族当天获得的票数
     */
    public static function GetDayFamilyTicket()
    {
        $time = strtotime('-1 days');
        $start_time = date('Y-m-d',$time);
        $end_time = date('Y-m-d',$time);
        $query = (new Query())
            ->select(['fa.family_id','IFNULL(sum(slm.real_tickets_num),0) as income_ticket'])
            ->from('mb_family fa')
            ->innerJoin('mb_family_member fm','fm.family_id=fa.family_id')
            ->innerJoin('mb_statistic_living_master slm','slm.user_id=fm.family_member_id')
            ->where('slm.statistic_time BETWEEN :stime and :endtime and slm.statistic_type=:type',[
                ':stime' => $start_time,
                ':endtime' => $end_time,
                ':type' => 2
            ])
            ->groupBy('fa.family_id')
            ->all();

        if($query === false || $query === null)
        {
            \Yii::getLogger()->log('统计家族当天获得的票数无数据：$query==:'.var_export($query,true),Logger::LEVEL_ERROR);
            return [];
        }
        return $query;
    }

    /**
     * 统计家族当天提现的票数
     */
    public static function GetDayFamilyTicketToCash()
    {
        $time = strtotime('-1 days');
        $start_time = date('Y-m-d',$time);
        $end_time = date('Y-m-d',$time);
        $query = (new Query())
            ->select(['fa.family_id','IFNULL(sum(ttc.ticket_num),0) as ticket_to_cash'])
            ->from('mb_family fa')
            ->innerJoin('mb_family_member fm','fm.family_id=fa.family_id')
            ->innerJoin('mb_ticket_to_cash ttc','ttc.user_id=fm.family_member_id')
            ->where('DATE_FORMAT(ttc.create_time,\'%Y-%m-%d\') BETWEEN :stime and :endtime and ttc.status not in(1,4)',[
                ':stime' => $start_time,
                ':endtime' => $end_time,
            ])
            ->groupBy('fa.family_id')
            ->all();
        if($query === false || $query === null)
        {
            \Yii::getLogger()->log('统计家族当天提现的票数无数据：$query==:'.var_export($query,true),Logger::LEVEL_ERROR);
            return [];
        }
        return $query;
    }

    public static function GetAllDayFamilyTicket(&$outInfo,&$error)
    {
        $ticket = self::GetDayFamilyTicket();  //获得的票数
        $ticket_to_cash = self::GetDayFamilyTicketToCash();  //提现的票数
        if(empty($ticket) && empty($ticket_to_cash))
        {
            \Yii::getLogger()->log('统计家族当天得到的票数和提现的票数都无数据',Logger::LEVEL_ERROR);
            $error = '统计数据为空';
            $outInfo = [];
            return false;
        }
        if(!empty($ticket) && !empty($ticket_to_cash))
        {
            $new_cash = [];
            foreach($ticket_to_cash as $cash)
            {
                   $new_cash[$cash['family_id']] = $cash;
            }

            $new_ticket_data = [];
            foreach($ticket as $key=>$ti)
            {
                $new_ticket_data[$key] = $ti;
                $new_ticket_data[$key]['ticket_to_cash'] = $new_cash[$ti['family_id']]['ticket_to_cash'];
            }
            $outInfo = $new_ticket_data;
            return true;
        }

        if(!empty($ticket) && empty($ticket_to_cash))
        {
            foreach($ticket as &$v)
            {
                $v['ticket_to_cash'] = 0;
            }
            $outInfo = $ticket;
            return true;
        }
        else
        {
            foreach($ticket_to_cash as &$to_cash)
            {
                $to_cash['income_ticket'] = 0;
            }
            $outInfo = $ticket_to_cash;
            return true;
        }

    }


    /**
     * 每个家族总票流水数据写入
     */
    public static function InsertDailyRecharge($arr,&$error){
        if(!is_array($arr)){
            $error = '参数非数组';
            return false;
        }

        $sql = 'insert into mb_statistic_family_ticket (family_id,income_ticket,ticket_to_cash,create_time) values';

        $params = [];
        $i = 1;
        $max = count($arr);
        $create_time = date('Y-m-d',strtotime('-1 day'));
        foreach($arr as $v){
            $params[':fid'.$i] = $v['family_id'];
            $params[':iticket'.$i] = $v['income_ticket'];
            $params[':tcash'.$i] = $v['ticket_to_cash'];
            $params[':ctime'.$i] = $create_time;
            $sql .= sprintf('(:fid%d,:iticket%d,:tcash%d,:ctime%d)',$i,$i,$i,$i);
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
            $error = '每个家族总票流水数据写入失败';
            return false;
        }
        return true;
    }


}