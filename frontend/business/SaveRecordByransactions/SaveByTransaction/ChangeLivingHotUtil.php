<?php
/**
 * 送礼物 人气，热门，周、月、日修改
 * User: hlq
 * Date: 2016/4/29
 * Time: 11:13
 */
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;
use common\models\Reward;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

/**
 * Class AttentionNumModifyByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class ChangeLivingHotUtil implements ISaveForTransaction
{
    private  $clientAvtive = null;

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$params)
    {
        $this->clientAvtive = $record;
        $this->params = $params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->clientAvtive instanceof Reward))
        {
            $error = '不是打赏用户';
            return false;
        }

        $sqlInsert = 'insert ignore into mb_time_livingmaster_ticketcount (livingmaster_id,hot_type,statistic_date,real_ticket_num,ticket_num,order_no) values(:luid,\'1\',:today,0,0,0)';
        $sqlInsertWeek = 'insert ignore into mb_time_livingmaster_ticketcount (livingmaster_id,hot_type,statistic_date,real_ticket_num,ticket_num,order_no) values(:luid,\'2\',:week,0,0,0)';
        $sqlInsertMonth ='insert ignore into mb_time_livingmaster_ticketcount (livingmaster_id,hot_type,statistic_date,real_ticket_num,ticket_num,order_no) values(:luid,\'3\',:month,0,0,0)';
        $sql = 'update mb_time_livingmaster_ticketcount set real_ticket_num= real_ticket_num + :gvalue ,ticket_num = ticket_num + :gvalue2 where livingmaster_id=:luid and hot_type=1 and statistic_date=:today';
        $sqlWeek = ' update mb_time_livingmaster_ticketcount set real_ticket_num= real_ticket_num + :gvalue ,ticket_num = ticket_num + :gvalue2 where livingmaster_id=:luid and hot_type=2 and statistic_date=:week';
        $sqlMonth =' update mb_time_livingmaster_ticketcount set real_ticket_num= real_ticket_num + :gvalue ,ticket_num = ticket_num + :gvalue2 where livingmaster_id=:luid and hot_type=3 and statistic_date=:month';

        $today = date('Y-m-d');
        \Yii::$app->db->createCommand($sqlInsert,
            [
                ':luid'=>$this->clientAvtive->living_master_id,
                ':today'=>$today,
            ])->execute();
        $rst = \Yii::$app->db->createCommand($sql,
            [
                ':luid'=>$this->clientAvtive->living_master_id,
                ':gvalue' => ($this->params['money_type']==1?$this->clientAvtive->gift_value:0),
                ':gvalue2' => $this->clientAvtive->gift_value,
                ':today'=>$today,
            ])->execute();
        if($rst <= 0)
        {
            throw new Exception('更新日票数失败');
        }
        $nowWeek = date('Y-W');
        \Yii::$app->db->createCommand($sqlInsertWeek,
            [
                ':luid'=>$this->clientAvtive->living_master_id,
                ':week'=>$nowWeek
            ])->execute();
        $rst = \Yii::$app->db->createCommand($sqlWeek,
            [
                ':luid'=>$this->clientAvtive->living_master_id,
                ':gvalue' => $this->clientAvtive->gift_value,
                ':gvalue2' => $this->clientAvtive->gift_value,
                ':week'=>$nowWeek
            ])->execute();
        if($rst <= 0)
        {
            throw new Exception('更新周票数失败');
        }
        $nowMonth = date('Y-m');
        \Yii::$app->db->createCommand($sqlInsertMonth,
            [
                ':luid'=>$this->clientAvtive->living_master_id,
                ':month'=>$nowMonth
            ])->execute();
        $rst = \Yii::$app->db->createCommand($sqlMonth,
            [
                ':luid'=>$this->clientAvtive->living_master_id,
                ':gvalue' => $this->clientAvtive->gift_value,
                ':gvalue2' => $this->clientAvtive->gift_value,
                ':month'=>$nowMonth
            ])->execute();
        if($rst <= 0)
        {
            throw new Exception('更新月票数失败');
        }
        return true;
    }


}