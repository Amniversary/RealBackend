<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/19
 * Time: 上午11:17
 */

namespace backend\business\SaveRecordByTransactions\SaveByTransaction;


use backend\business\AuthorizerUtil;
use backend\business\SaveRecordByTransactions\ISaveForTransaction;
use backend\business\WeChatUserUtil;

class TestTrans implements ISaveForTransaction
{
    public $record;
    public $extend;

    public function __construct($record,$extend = [])
    {
        $this->record =  $record;
        $this->extend = $extend;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $time = date('Y-m-d');
        $date = date('Y-m-d H:i:s');
        $temp = AuthorizerUtil::isVerify($this->record['verify_type_info']);

        $sql = 'insert ignore into wc_fans_statistics(app_id,new_user,cancel_user,net_user,total_user,statistics_date) VALUES(:ap,0,0,0,0,:tm)';
        \Yii::$app->db->createCommand($sql, [
            ':ap' => $this->record['record_id'],
            ':tm' => $time
        ])->execute();

        $insersql = 'insert ignore into wc_statistics_count(app_id,count_user,cumulate_user,update_time) VALUES (:ap,0,0,:date)';
        \Yii::$app->db->createCommand($insersql, [
            ':ap' => $this->record['record_id'],
            ':date' => $date,
        ])->execute();

        if ($temp) {
            //TODO： 获取公众号粉丝数
            if (!WeChatUserUtil::getWxFansAccumulate($this->record['authorizer_access_token'], $res, $error)) {
                \Yii::error('获取粉丝数失败 :' . $error);
                return false;
            }
            $data = $res['list'][0];
            $upsql = 'update wc_fans_statistics set total_user = :total WHERE app_id = :appid and statistics_date = :date';
            $rst = \Yii::$app->db->createCommand($upsql, [
                ':appid' => $this->record['record_id'],
                ':total' => $data['cumulate_user'],
                ':date' => $time
            ])->execute();
            if ($rst <= 0) {
                $error = '更新每日统计粉丝信息失败';
                \Yii::error($error . ' : ' . \Yii::$app->db->createCommand($upsql, [
                        ':appid' => $this->record['record_id'],
                        ':total' => $data['cumulate_user'],
                        ':cumu' => $data['cumulate_user'],
                        ':rma' => $data['cumulate_user'],
                        ':date' => $time])->rawSql);
                return false;
            }

            $up_count = 'update wc_statistics_count set count_user = :cu,cumulate_user = :cumu WHERE app_id = :apd';
            $result = \Yii::$app->db->createCommand($up_count, [
                ':cu' => $data['cumulate_user'],
                ':cumu' => $data['cumulate_user'],
                ':apd' => $this->record['record_id'],
            ])->execute();
            if ($result <= 0) {
                $error = '更新粉丝累计统计失败:';
                \Yii::error($error . ' :' . \Yii::$app->db->createCommand($up_count, [
                        ':cu' => $data['cumulate_user'],
                        ':cumu' => $data['cumulate_user'],
                        ':apd' => $this->record['record_id'],
                    ])->rawSql);
                return false;
            }
        }
        return true;
    }
}