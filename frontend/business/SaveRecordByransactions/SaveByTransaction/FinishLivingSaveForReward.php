<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class FinishLivingSaveForReward implements ISaveForTransaction
{
    private  $params=[];

    /**
     * @param $data   所要更新的数据
     * @throws Exception
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $sql = 'update mb_living set `finish_time`=:ftime,`status`=:tag,`living_time`=:ltime where living_id=:id and status=2';
        $living_result = \Yii::$app->db->createCommand($sql,[
            ':ftime' => date('Y-m-d H:i:s',time()),
            ':tag' => 0,
            ':ltime' => $this->params['living_time'],
            ':id' => $this->params['living_id']
        ])->execute();

        if($living_result <= 0){
            $arr = [
                'errno'=>'1105',
                'errmsg' =>'直播已结束'
            ];
            $error = $arr;
            \Yii::getLogger()->log('直播已结束 sql:'. \Yii::$app->db->createCommand($sql,[
                    ':ftime' => date('Y-m-d H:i:s',time()),
                    ':tag' => 0,
                    ':ltime' => $this->params['living_time'],
                    ':id' => $this->params['living_id']
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        $sql = 'update mb_living_statistics set finish_time=:ftime,living_time=:ltime,goods_num=:gnum,tickets_num=:tnum,person_count_total=:ptotal,hot_num=:hnum,is_to_expirence=1,tickets_real_num=:rnum,living_second_time=:stime where living_before_id=:lbid and living_master_id=:lmid';
        $statistics_result = \Yii::$app->db->createCommand($sql,[
            ':ftime' => date('Y-m-d H:i:s',time()),
            ':ltime' => $this->params['living_time'],
            ':gnum' => $this->params['goods_num'],
            ':tnum' => $this->params['tickets_num'],
            ':hnum' => $this->params['hot_num'],
            ':ptotal' => $this->params['person_count_total'],
            ':lbid' => $this->params['living_before_id'],
            ':lmid' => $this->params['living_master_id'],
            ':rnum' => $this->params['tickets_real_num'],
            ':stime' => $this->params['living_second_time']
        ])->execute();

        if($statistics_result <= 0){
            $error = '直播记录3更新失败';
            \Yii::getLogger()->log('直播记录3更新失败 sql:'. \Yii::$app->db->createCommand($sql,[
                    ':ftime' => date('Y-m-d H:i:s',time()),
                    ':ltime' => $this->params['living_time'],
                    ':gnum' => $this->params['goods_num'],
                    ':tnum' => $this->params['tickets_num'],
                    ':hnum' => $this->params['hot_num'],
                    ':ptotal' => $this->params['person_count_total'],
                    ':lbid' => $this->params['living_before_id'],
                    ':lmid' => $this->params['living_master_id'],
                    ':rnum' => $this->params['tickets_real_num'],
                    ':stime' => $this->params['living_second_time']
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }
        return true;

    }
}