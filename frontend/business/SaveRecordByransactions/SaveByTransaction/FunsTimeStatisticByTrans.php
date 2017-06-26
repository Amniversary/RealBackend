<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\ClientActive;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

/**
 * 粉丝数统计变化更新
 * Class AttentionNumModifyByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class FunsTimeStatisticByTrans implements ISaveForTransaction
{
    private  $clientAvtive = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->clientAvtive = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->clientAvtive instanceof ClientActive))
        {
            $error = '不是用户活跃记录4';
            return false;
        }
        $op_type = $this->extend_params['op_type'];
        if(!in_array($op_type,['attention','cancel']))
        {
            $error = '操作类型异常';
            return false;
        }
        if($op_type === 'attention')
        {
            $sqlInsert = 'insert ignore into mb_time_funs_count (user_id,hot_type,statistic_date,funs_num,funs_sum_num,order_no) values(:uid,\'1\',:today,0,0,0)';
            $sqlInsertWeek = 'insert ignore into mb_time_funs_count (user_id,hot_type,statistic_date,funs_num,funs_sum_num,order_no) values(:uid,\'2\',:week,0,0,0)';
            $sqlInsertMonth ='insert ignore into mb_time_funs_count (user_id,hot_type,statistic_date,funs_num,funs_sum_num,order_no) values(:uid,\'3\',:month,0,0,0)';
            $sql = ' update mb_time_funs_count set funs_num= funs_num + 1 ,funs_sum_num = funs_sum_num + 1 where user_id=:uid and hot_type=1 and statistic_date=:today';
            $sqlWeek = ' update mb_time_funs_count set funs_num= funs_num + 1 ,funs_sum_num = funs_sum_num + 1 where user_id=:uid and hot_type=2 and statistic_date=:week';
            $sqlMonth =' update mb_time_funs_count set funs_num= funs_num + 1 ,funs_sum_num = funs_sum_num + 1 where user_id=:uid and hot_type=3 and statistic_date=:month';
        }
        else
        {
            $sqlInsert = 'insert ignore into mb_time_funs_count (user_id,hot_type,statistic_date,funs_num,funs_sum_num,order_no) values(:uid,\'1\',:today,0,0,0)';
            $sqlInsertWeek = 'insert ignore into mb_time_funs_count (user_id,hot_type,statistic_date,funs_num,funs_sum_num,order_no) values(:uid,\'2\',:week,0,0,0)';
            $sqlInsertMonth ='insert ignore into mb_time_funs_count (user_id,hot_type,statistic_date,funs_num,funs_sum_num,order_no) values(:uid,\'3\',:month,0,0,0)';
            $sql = ' update mb_time_funs_count set funs_num= funs_num - 1 ,funs_sum_num = funs_sum_num + 1 where user_id=:uid and hot_type=1 and statistic_date=:today';
            $sqlWeek = ' update mb_time_funs_count set funs_num= funs_num - 1 ,funs_sum_num = funs_sum_num + 1 where user_id=:uid and hot_type=2 and statistic_date=:week';
            $sqlMonth =' update mb_time_funs_count set funs_num= funs_num - 1 ,funs_sum_num = funs_sum_num + 1 where user_id=:uid and hot_type=3 and statistic_date=:month';
        }
        $today = date('Y-m-d');
        \Yii::$app->db->createCommand($sqlInsert,
            [
                ':uid'=>$this->clientAvtive->user_id,
                ':today'=>$today,
            ])->execute();
        $rst = \Yii::$app->db->createCommand($sql,
            [
                ':uid'=>$this->clientAvtive->user_id,
                ':today'=>$today,
            ])->execute();
        if($rst <= 0)
        {
            throw new Exception('更新日粉丝数失败');
        }
        $nowWeek = date('Y-W');
        \Yii::$app->db->createCommand($sqlInsertWeek,
            [
                ':uid'=>$this->clientAvtive->user_id,
                ':week'=>$nowWeek
            ])->execute();
        $rst = \Yii::$app->db->createCommand($sqlWeek,
            [
                ':uid'=>$this->clientAvtive->user_id,
                ':week'=>$nowWeek
            ])->execute();
        if($rst <= 0)
        {
            throw new Exception('更新周粉丝数失败');
        }
        $nowMonth = date('Y-m');
        \Yii::$app->db->createCommand($sqlInsertMonth,
            [
                ':uid'=>$this->clientAvtive->user_id,
                ':month'=>$nowMonth
            ])->execute();
        $rst = \Yii::$app->db->createCommand($sqlMonth,
            [
                ':uid'=>$this->clientAvtive->user_id,
                ':month'=>$nowMonth
            ])->execute();
        if($rst <= 0)
        {
            throw new Exception('更新月粉丝数失败');
        }
        return true;
    }
} 