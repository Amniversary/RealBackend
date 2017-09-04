<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/13
 * Time: 下午2:01
 */

namespace backend\business\SaveRecordByTransactions\SaveByTransaction;


use backend\business\SaveRecordByTransactions\ISaveForTransaction;
use common\models\FansDate;
use common\models\FansStatistics;

class StatisticFansUserByTrans implements ISaveForTransaction
{
    public $record;
    public $extend;

    public function __construct($data, $extend_params = [])
    {
        $this->record = $data;
        $this->extend = $extend_params;
    }

    function SaveRecordForTransaction(&$error, &$outInfo)
    {
        $sql = '';
        $upsql = '';
        $day_sql = '';
        $time = date('Y-m-d');
        $day_time = intval(date('H'));
        $DayInfo = FansDate::findOne(['app_id' => $this->record['record_id'], 'statistics_date' => $day_time, 'create_time' => $time]);
        $DataInfo = FansStatistics::findOne(['app_id' => $this->record['record_id'], 'statistics_date' => $time]);
        if (empty($DataInfo)) {  //TODO: 统计每日数据
            $insql = 'insert ignore into wc_fans_statistics(app_id,new_user,cancel_user,net_user,total_user,
statistics_date) VALUES (:appid,0,0,0,0,:tme)';
            \Yii::$app->db->createCommand($insql, [
                ':appid' => $this->record['record_id'],
                ':tme' => $time,
            ])->execute();
        }
        if (empty($DayInfo)) {   //TODO: 统计每小时数据
            $daysql = 'insert ignore into wc_fans_date(app_id,new_user,cancel_user,net_user,statistics_date,create_time)
VALUES(:apd, 0, 0, 0, :dt, :cre)';
            \Yii::$app->db->createCommand($daysql, [
                ':apd' => $this->record['record_id'],
                ':dt' => $day_time,
                ':cre' => $time
            ])->execute();
        }
        $sql .= 'update wc_fans_statistics set ';
        $upsql .= 'update wc_statistics_count set ';
        $day_sql .= 'update wc_fans_date set ';
        if ($this->extend['type'] == 1) {
            $sql .= 'new_user = new_user + 1,net_user = net_user + 1';
            $upsql .= 'count_user = count_user + 1,cumulate_user = cumulate_user + 1,update_time = :dtim';
            $day_sql .= 'new_user = new_user + 1, net_user = net_user + 1';
        } else {
            $sql .= 'cancel_user = cancel_user + 1,net_user = net_user - 1';
            $upsql .= 'count_user = if(count_user > 0,count_user - 1,0),update_time = :dtim';
            $day_sql .= 'cancel_user = cancel_user + 1, net_user = net_user -1';
        }
        $sql .= ' WHERE app_id = :ap and statistics_date =:tim';
        $upsql .= ' WHERE app_id = :appid';
        $day_sql .= ' WHERE app_id = :apd and statistics_date = :dt and create_time = :cre';
        $res = \Yii::$app->db->createCommand($sql, [
            ':ap' => $this->record['record_id'],
            ':tim' => $time
        ])->execute();
        if ($res <= 0) {
            $error = '保存关注数失败';
            \Yii::error($error . ' :' . \Yii::$app->db->createCommand($sql, [
                    ':ap' => $this->record['record_id'],
                    ':tim' => $time
                ])->rawSql);
            return false;
        }

        $rst = \Yii::$app->db->createCommand($upsql, [
            ':dtim' => date('Y-m-d H:i:s'),
            ':appid' => $this->record['record_id'],
        ])->execute();
        if ($rst <= 0) {
            $error = '保存累计关注数失败';
            \Yii::error($error . ' : ' . \Yii::$app->db->createCommand($upsql, [
                    ':dtim' => date('Y-m-d H:i:s'),
                    ':appid' => $this->record['record_id'],
                ])->rawSql);
            return false;
        }
        $ret = \Yii::$app->db->createCommand($day_sql, [
            ':apd' => $this->record['record_id'],
            ':dt' => $day_time,
            ':cre' => $time,
        ])->execute();
        if($ret <= 0) {
            $error = '保存每日关注数失败';
            \Yii::error($error . ' : '. \Yii::$app->db->createCommand($day_sql, [
                    ':apd' => $this->record['record_id'],
                    ':dt' => $day_time,
                    ':cre' => $time,
                ])->rawSql);
            return false;
        }

        return true;
    }
}