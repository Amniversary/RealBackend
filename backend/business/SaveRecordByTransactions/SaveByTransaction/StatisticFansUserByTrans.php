<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/13
 * Time: 下午2:01
 */

namespace backend\business\SaveRecordByTransactions\SaveByTransaction;


use backend\business\SaveRecordByTransactions\ISaveForTransaction;

class StatisticFansUserByTrans implements ISaveForTransaction
{
    public $record;
    public $extend;

    public function __construct($data ,$extend_params = [])
    {
        $this->record = $data;
        $this->extend = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $sql = '';
        $upsql = '';
        $time = date('Y-m-d');
        $insql = 'insert ignore into wc_fans_statistics(app_id,new_user,cancel_user,net_user,total_user,
statistics_date) VALUES (:appid,0,0,0,0,:tme)';
        \Yii::$app->db->createCommand($insql,[
            ':appid'=>$this->record['record_id'],
            ':tme'=>$time,
        ])->execute();

        $sql .= 'update wc_fans_statistics set ';
        $upsql .= 'update wc_statistics_count set ';
        if($this->extend['type'] == 1){
            $sql .= 'new_user = new_user + 1,net_user = net_user + 1';
            $upsql .= 'count_user = count_user + 1,cumulate_user = cumulate_user + 1,update_time = :dtim';
        }else{
            $sql .= 'cancel_user = cancel_user + 1,net_user = net_user - 1';
            $upsql .= 'count_user = if(count_user > 0,count_user - 1,0),update_time = :dtim';
        }
        $sql .= ' WHERE app_id = :ap and statistics_date =:tim';
        $upsql.= ' WHERE app_id = :appid';
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

        $rst = \Yii::$app->db->createCommand($upsql,[
            ':dtim'=>date('Y-m-d H:i:s'),
            ':appid'=>$this->record['record_id'],
        ])->execute();
        if($rst <= 0){
            $error = '保存累计关注数失败';
            \Yii::error($error. ' : '. \Yii::$app->db->createCommand($upsql,[
                    ':dtim'=>date('Y-m-d H:i:s'),
                    ':appid'=>$this->record['record_id'],
                ])->rawSql);
            return false;
        }

        return true;
    }
}