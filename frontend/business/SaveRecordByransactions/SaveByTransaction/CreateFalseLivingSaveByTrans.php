<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use Faker\Provider\Uuid;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\db\Query;
use yii\log\Logger;

/**
 * 增加假直播数据
 * Class CreateFalseLivingSaveByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class CreateFalseLivingSaveByTrans implements ISaveForTransaction
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
        $InsertSql = 'insert into mb_living (heart_count,is_to_expirence,living_before_id,is_official,order_no,status,device_type,living_title,living_master_id,city,longitude,latitude,op_unique_no,living_type,room_no,create_time,living_pic_url)
 VALUES (0,0,1,0,4000,2,:dtype,:title,:maste_id,:city,:lon,:lat,:unique_no,:living_type,:rno,:ctime,:lpic)';

        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':dtype' => 0,
            ':maste_id' => $this->data['data']['living_master_id'],
            ':title' => '',
            ':city' => '',
            ':lon' => 0,
            ':lat' => 0,
            ':unique_no' => Uuid::uuid(),
            ':living_type' => 5,
            ':rno' => $this->data['data']['room_no'],
            ':ctime' => date('Y-m-d H:i:s'),
            ':lpic' => $this->data['data']['living_pic_url'],
        ])->execute();

        if($res_insert <= 0){
            $error = '创建假直播记录1写入失败';
            \Yii::getLogger()->log(\Yii::$app->db->createCommand($error.':'.$InsertSql,[
                ':dtype' => 0,
                ':maste_id' => $this->data['data']['living_master_id'],
                ':title' => '',
                ':city' => '',
                ':lon' => 0,
                ':lat' => 0,
                ':unique_no' => Uuid::uuid(),
                ':living_type' => 5,
                ':rno' => $this->data['data']['room_no'],
                ':ctime' => date('Y-m-d H:i:s'),
                ':lpic' => $this->data['data']['living_pic_url'],
            ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        if(empty($this->data['data']['living_id'])){
            $this->data['data']['living_id'] = 0;
        }
        //$person_num = LivingUtil::GetLivingOnlinePerson($this->data['data']['living_id']);  //得到以前直播未离开房间的用户
//        \Yii::getLogger()->log('person_num===:'.$person_num['person_count'].'-----living_id==='.$this->data['data']['living_id'],Logger::LEVEL_ERROR);

        $sql = 'SELECT LAST_INSERT_ID()';
        $living_id = \Yii::$app->db->createCommand($sql)->queryScalar();

        $sql = 'insert ignore into mb_living_statistics (living_before_id,living_title,living_master_id,is_to_expirence,goods_num,
tickets_num,person_count_total,hot_num,city,longitude,latitude,living_second_time,create_time,room_no,living_type)
values (:lbid,:ltitle,:lmid,:ex,:gnum,:tnum,:ptotal,:hnum,:city,:lon,:lat,:ltime,:ctime,:rno,:ltype)';
        $res_insert = \Yii::$app->db->createCommand($sql,[
            ':lbid' => 1,
            ':ltitle' => '',
            ':lmid' => $this->data['data']['living_master_id'],
            ':ex' => 0,
            ':gnum' => 0,
            ':tnum' => 0,
            ':ptotal' => 0,//$person_num['person_count'],
            ':hnum' => 0,
            ':city' => '',
            ':lon' => 0,
            ':lat' => 0,
            ':ltime' => 0,
            ':ctime' => date('Y-m-d H:i:s'),
            ':rno' => $this->data['data']['room_no'],
            ':ltype' => 5,
        ])->execute();

        if($res_insert <= 0){
            $error = '直播记录2写入失败';
            \Yii::getLogger()->log($error.'sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':lbid' => 1,
                    ':ltitle' => '',
                    ':lmid' => $this->data['data']['living_master_id'],
                    ':ex' => 0,
                    ':gnum' => 0,
                    ':tnum' => 0,
                    ':ptotal' => 0,//$person_num['person_count'],
                    ':hnum' => 0,
                    ':city' => $this->data['data']['city'],
                    ':lon' => 0,
                    ':lat' => 0,
                    ':ltime' => 0,
                    ':ctime' => date('Y-m-d H:i:s'),
                    ':rno' => $this->data['data']['room_no'],
                    ':ltype' => 5,
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        //直播人数表
        $InsertSql = 'insert  into mb_living_personnum (living_id,person_count,person_count_total) VALUES (:id,:num,:total)';
        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':id' => $living_id,
            ':num' => 0,
            ':total' => 0
        ])->execute();
        if($res_insert <= 0){
            $error = '直播记录3写入失败';
            \Yii::getLogger()->log($error.'sql===:'.\Yii::$app->db->createCommand($InsertSql,[
                    ':id' => $living_id,
                    ':num' => 0,
                    ':total' => 0
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        $InsertSql = 'insert into mb_chat_room (living_id,room_master_id,create_time,manager_num,cur_manager_num,other_id,status,public,approval)
VALUES (:lid,:cmid,:ctime,:num,:mnum,:oid,:tag,:pub,:app)';


        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':lid' =>$living_id,
            ':cmid' =>$this->data['data']['room_master_id'],
            ':ctime' =>date('Y-m-d H:i:s',time()),
            ':num' =>6,
            ':mnum' =>0,
            ':oid' =>Uuid::uuid(),
            ':tag' =>1,
            ':pub' =>1,
            ':app' =>1,
        ])->execute();
        //不做判断，可能第二次更新
        if($res_insert <= 0)
        {
            $error = '直播记录4更新失败';
            \Yii::getLogger()->log($error.'sql===:'.\Yii::$app->db->createCommand($InsertSql,[
                    ':lid' =>$living_id,
                    ':cmid' =>$this->data['data']['living_master_id'],
                    ':ctime' =>date('Y-m-d H:i:s',time()),
                    ':num' =>6,
                    ':mnum' =>0,
                    ':oid' =>'',
                    ':tag' =>1,
                    ':pub' =>1,
                    ':app' =>1,
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }
        return true;

    }
}