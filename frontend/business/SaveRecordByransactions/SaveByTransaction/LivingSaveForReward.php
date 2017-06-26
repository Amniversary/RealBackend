<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;

class LivingSaveForReward implements ISaveForTransaction
{
    private  $data=[];

    /**
     * @param $data   所要插入的数据
     * @throws Exception
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $sql = 'insert ignore into mb_living (living_title,living_master_id,city,longitude,latitude,op_unique_no)
 VALUES (:title,:maste_id,:city,:lon,:lat,:unique_no )';
        $result = \Yii::$app->db->createCommand($sql,[
            ':title' => $this->data['living_title'],
            ':maste_id' => $this->data['living_master_id'],
            ':city' => $this->data['city'],
            ':lon' => $this->data['longitude'],
            ':lat' => $this->data['latitude'],
            ':unique_no' => $this->data['op_unique_no']
        ])->execute();
        if($result <= 0){
            $error = '直播记录1写入失败';
            return false;
        }
        $sql = 'SELECT LAST_INSERT_ID()';
        $result = \Yii::$app->db->createCommand($sql)->queryScalar();
        $living_id = $result;

        //直播点赞数表
        $sql = 'insert ignore into mb_living_goods (living_id,goods_num) VALUES (:id,:num)';
        $result = \Yii::$app->db->createCommand($sql,[
            ':id' => $living_id,
            ':num' => 0
        ])->execute();

        if($result <= 0){
            $error = '直播记录2写入失败';
            return false;
        }

        //直播票数表
        $sql = 'insert ignore into mb_living_tickets (living_id,tickets_num) VALUES (:id,:num)';
        $result = \Yii::$app->db->createCommand($sql,[
            ':id' => $living_id,
            ':num' => 0
        ])->execute();

        if($result <= 0){
            $error = '直播记录3写入失败';
            return false;
        }

        //直播人数表
        $sql = 'insert ignore into mb_living_personnum (living_id,person_count,person_count_total) VALUES (:id,:num,:total)';
        $result = \Yii::$app->db->createCommand($sql,[
            ':id' => $living_id,
            ':num' => 0,
            ':total' => 0
        ])->execute();

        if($result <= 0){
            $error = '直播记录4写入失败';
            return false;
        }

        //直播热门表
        $sql = 'insert ignore into mb_living_hot (living_id,hot_num) VALUES (:id,:num)';
        $result = \Yii::$app->db->createCommand($sql,[
            ':id' => $living_id,
            ':num' => 0,
        ])->execute();

        if($result <= 0){
            $error = '直播记录5写入失败';
            return false;
        }

        $outInfo['living_id'] = $living_id;
        return true;

    }
}