<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\db\Query;
use yii\log\Logger;

class CreateLivingSaveByTrans implements ISaveForTransaction
{
    private  $data = [];
    private  $extend = [];

    /**
     * @param $data //所要插入的数据
     * @param array $extend_params
     */
    public function __construct($data, $extend_params = [])
    {
        $this->data = $data;
        $this->extend = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $InsertSql = 'insert ignore into mb_living (heart_count,is_to_expirence,living_before_id,is_official,
order_no,status,device_type,living_title,living_master_id,city,longitude,latitude,op_unique_no,living_type,app_id)
 VALUES (0,0,0,0,4000,1,:dtype,:title,:maste_id,:city,:lon,:lat,:unique_no,:living_type,:appid)';

        $UpdateSql = 'update mb_living set finish_time=0,living_time=:ltime,create_time=:ctime,finish_time=:ftime,is_to_expirence=0,living_before_id=living_before_id+1,is_official=0,order_no=4000,status=1,device_type=:dtype,living_title=:title,city=:city,longitude=:lon,latitude=:lat,heart_count=0,game_name=:gname,room_no=:rno,living_type=:living_type,app_id=:appid where living_master_id=:lid';


        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':dtype' => $this->data['device_type'],
            ':maste_id' => $this->data['living_master_id'],
            ':title' => $this->data['living_title'],
            ':city' => $this->data['city'],
            ':lon' => $this->data['longitude'],
            ':lat' => $this->data['latitude'],
            ':unique_no' => $this->data['op_unique_no'],
            ':living_type' => $this->data['living_type'],
            ':appid' => $this->data['app_id'],
        ])->execute();

        $res_update = \Yii::$app->db->createCommand($UpdateSql,[
            ':ltime' => '',
            ':ctime' => date('Y-m-d H:i:s'),
            ':ftime' => date('Y-m-d H:i:s'),
            ':dtype' => $this->data['device_type'],
            ':title' => $this->data['living_title'],
            ':city' => $this->data['city'],
            ':lon' => $this->data['longitude'],
            ':lat' => $this->data['latitude'],
            ':lid' => $this->data['living_master_id'],
            ':gname' => '',
            ':rno' => $this->data['room_no'],
            ':living_type' => $this->data['living_type'],
            ':appid' => $this->data['app_id'],
        ])->execute();


        if($res_update <= 0){
            $error = '创建直播记录1写入失败';
            \Yii::getLogger()->log(\Yii::$app->db->createCommand($error.':'.$UpdateSql,[
                ':ltime' => '',
                ':ctime' => date('Y-m-d H:i:s'),
                ':ftime' => date('Y-m-d H:i:s'),
                ':dtype' => $this->data['device_type'],
                ':title' => $this->data['living_title'],
                ':city' => $this->data['city'],
                ':lon' => $this->data['longitude'],
                ':lat' => $this->data['latitude'],
                ':lid' => $this->data['living_master_id'],
            ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }
        else
        {
            \Yii::getLogger()->log('创建直播1成功，user_id:'.$this->data['living_master_id'],Logger::LEVEL_ERROR);
        }

        if(empty($this->data['living_id'])){
            $this->data['living_id'] = 0;
        }
        //$person_num = LivingUtil::GetLivingOnlinePerson($this->data['living_id']);  //得到以前直播未离开房间的用户
//        \Yii::getLogger()->log('person_num===:'.$person_num['person_count'].'-----living_id==='.$this->data['living_id'],Logger::LEVEL_ERROR);

        //$sql = 'SELECT LAST_INSERT_ID()';

        $result = (new Query())
            ->select(['living_id','living_before_id'])
            ->from('mb_living')
            ->where('living_master_id=:lmid',[':lmid' => $this->data['living_master_id']])
            ->one();
        $living_id = $result['living_id'];



        $sql = 'insert ignore into mb_living_statistics (living_before_id,living_title,living_master_id,
is_to_expirence,goods_num,tickets_num,person_count_total,hot_num,city,longitude,latitude,living_second_time,
create_time,room_no,app_id)
values (:lbid,:ltitle,:lmid,:ex,:gnum,:tnum,:ptotal,:hnum,:city,:lon,:lat,:ltime,:ctime,:rno,:appid)';
        $res_insert = \Yii::$app->db->createCommand($sql,[
            ':lbid' => $result['living_before_id'],
            ':ltitle' => $this->data['living_title'],
            ':lmid' => $this->data['living_master_id'],
            ':ex' => 0,
            ':gnum' => 0,
            ':tnum' => 0,
            ':ptotal' => 0,//$person_num['person_count'],
            ':hnum' => 0,
            ':city' => $this->data['city'],
            ':lon' => $this->data['longitude'],
            ':lat' => $this->data['latitude'],
            ':ltime' => 0,
            ':ctime' => date('Y-m-d H:i:s'),
            ':rno' => $this->data['room_no'],
            ':appid' => $this->data['app_id'],
        ])->execute();

        if($res_insert <= 0){
            $error = '直播记录7写入失败';
            \Yii::getLogger()->log($error.'sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':lbid' => $result['living_before_id'],
                    ':ltitle' => $this->data['living_title'],
                    ':lmid' => $this->data['living_master_id'],
                    ':ex' => 0,
                    ':gnum' => 0,
                    ':tnum' => 0,
                    ':ptotal' => 0,//$person_num['person_count'],
                    ':hnum' => 0,
                    ':city' => $this->data['city'],
                    ':lon' => $this->data['longitude'],
                    ':lat' => $this->data['latitude'],
                    ':ltime' => 0,
                    ':ctime' => date('Y-m-d H:i:s'),
                    ':rno' => $this->data['room_no'],
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }


        //直播点赞数表
        $InsertSql = 'insert ignore into mb_living_goods (living_id,goods_num) VALUES (:id,:num)';
        $UpdateSql = 'update mb_living_goods set goods_num=:num,remark1=:ttime where living_id=:lid';

        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':id' => $living_id,
            ':num' => -1
        ])->execute();

        $res_update = \Yii::$app->db->createCommand($UpdateSql,[
            ':lid' => $living_id,
            ':num' => 0,
            ':ttime' => (string)time()
        ])->execute();

//        \Yii::getLogger()->log('sql:'.\Yii::$app->db->createCommand($InsertSql,[
//                ':id' => $living_id,
//                ':num' => -1
//            ])->rawSql,Logger::LEVEL_ERROR);
//
//        \Yii::getLogger()->log('sql:'.\Yii::$app->db->createCommand($UpdateSql,[
//                ':lid' => $living_id,
//                ':num' => 0
//            ])->rawSql,Logger::LEVEL_ERROR);

        if($res_update <= 0){

            $error = '直播记录2写入失败';
            \Yii::getLogger()->log('直播记录2写入失败：'.\Yii::$app->db->createCommand($UpdateSql,[
                    ':lid' => $living_id,
                    ':num' => 0,
                    ':ttime' => (string)time()
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        //直播票数表
        $InsertSql = 'insert ignore into mb_living_tickets (living_id,tickets_num,tickets_real_num) VALUES (:id,:num,0)';

        $UpdateSql = 'update mb_living_tickets set tickets_num=0,tickets_real_num=0,remark1=:ttime where living_id=:lid';

        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':id' => $living_id,
            ':num' => -1
        ])->execute();

        $res_update = \Yii::$app->db->createCommand($UpdateSql,[
            ':lid' => $living_id,
            ':ttime' => (string)time()
        ])->execute();

        if($res_update <= 0){
            $error = '直播记录3写入失败';
            \Yii::getLogger()->log('直播记录3写入失败:'. \Yii::$app->db->createCommand($UpdateSql,[
                    ':lid' => $living_id,
                    ':ttime' => (string)time()
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        //直播人数表
        $InsertSql = 'insert ignore into mb_living_personnum (living_id,person_count,person_count_total) VALUES (:id,:num,:total)';
        $UpdateSql = 'update mb_living_personnum set person_count=:num,person_count_total=:total,remark1=:ttime where living_id=:lid';
        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':id' => $living_id,
            ':num' => -1,//$person_num['person_count'],
            ':total' => -1 //$person_num['person_count']
        ])->execute();

        $res_update = \Yii::$app->db->createCommand($UpdateSql,[
            ':lid' => $living_id,
            ':num' => 0,//$person_num['person_count'],
            ':total' => 0,//$person_num['person_count'],
            ':ttime' => (string)time()
        ])->execute();

        if($res_update <= 0){
            $error = '直播记录4写入失败';
            return false;
        }



        //直播热门表
        $InsertSql = 'insert ignore into mb_living_hot (living_id,hot_num,order_no,living_num) VALUES (:id,:num,:ordern,:lnum)';
        $UpdateSql = 'update mb_living_hot set hot_num=0,order_no=if(living_num = 0,4000,living_num -1),remark1=:ttime,living_num=if(living_num > 0,living_num-1,0) where living_id=:lid';
        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':id' => $living_id,
            ':num' => -1,
            ':ordern' => 4000,
            ':lnum' => 0,
        ])->execute();

//        if($res_insert <= 0)
//        {
//            $error = '初始化热门直播记录失败';
//            \Yii::getLogger()->log('living_hot_sql=:'.\Yii::$app->db->createCommand($InsertSql,[
//                    ':id' => $living_id,
//                    ':num' => -1,
//                    ':ordern' => 4000,
//                    ':lnum' => 0,
//                ])->rawSql,Logger::LEVEL_ERROR);
//            return false;
//        }

        $res_update = \Yii::$app->db->createCommand($UpdateSql,[
            ':lid' => $living_id,
            ':ttime' => (string)time(),
        ])->execute();

        if($res_update <= 0){
            $error = '直播记录5写入失败';
            return false;
        }

        //主播人气表
        $InsertSql = 'insert ignore into mb_livingmaster_hot (livingmaster_id,hot_type,hot_num,order_no) VALUES (:luid,:htype1,:num1,:ordern1),(:luid,:htype2,:num2,:ordern2),(:luid,:htype3,:num3,:ordern3);';

        $UpdateSql = 'update mb_livingmaster_hot set hot_num=0,order_no=4000,remark1=:ttime where livingmaster_id=:luid';

        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':luid' => $this->data['living_master_id'],
            ':htype1' => 1,
            ':htype2' => 2,
            ':htype3' => 3,
            ':num1' => -1,
            ':num2' => -1,
            ':num3' => -1,
            ':ordern1' => 4000,
            ':ordern2' => 4000,
            ':ordern3' => 4000,
        ])->execute();

        $res_update = \Yii::$app->db->createCommand($UpdateSql,[
            ':luid' => $this->data['living_master_id'],
            ':ttime' => (string)time()
        ])->execute();

        if($res_update <= 0){
            $error = '直播记录6写入失败';
            return false;
        }

        //删除旧数据
        $sql_query =(new Query())
            ->select(['private_id'])
            ->from('mb_living_private')
            ->where('living_id=:lid and living_master_id=:lmid',
                [
                    ':lid' => $living_id,
                    ':lmid' => $this->data['living_master_id'],
                ])->one();
        if(!empty($sql_query))
        {
            $sql = 'delete from mb_living_private_views WHERE private_id=:pid';
            $del_res = \Yii::$app->db->createCommand($sql,[
                ':pid' => $sql_query['private_id'],
            ])->execute();

            $del_sql = 'delete from mb_living_private WHERE private_id=:pid';
            $del_res = \Yii::$app->db->createCommand($del_sql,[
                ':pid' => $sql_query['private_id'],
            ])->execute();
        }

        $sql_query =(new Query())
            ->select(['tikcet_id'])
            ->from('mb_living_passwrod_ticket')
            ->where('living_id=:lid and living_master_id=:lmid',
                [
                    ':lid' => $living_id,
                    ':lmid' => $this->data['living_master_id'],
                ])->one();
        if(!empty($sql_query))
        {
            $sql = 'delete from mb_living_passwrod_ticket_views WHERE tikcet_id=:tid';
            $del_res = \Yii::$app->db->createCommand($sql,[
                ':tid' => $sql_query['tikcet_id'],
            ])->execute();

            $del_sql = 'delete from mb_living_passwrod_ticket WHERE tikcet_id=:tid';
            $del_res = \Yii::$app->db->createCommand($del_sql,[
                ':tid' => $sql_query['tikcet_id'],
            ])->execute();
        }

        if($this->data['living_type'] == 3)   //密码直播
        {
            $InsertSql = 'insert ignore into mb_living_private (living_id,living_before_id,password,living_master_id,room_no) VALUES (:lid,:lbid,:pwd,:lmid,:rno)';
            $UpdateSql = 'update mb_living_private set remark1=:rem1 where living_id=:lid and living_before_id=:lbid and living_master_id=:lmid';
            $res_insert = \Yii::$app->db->createCommand($InsertSql,[
                ':lid' => $living_id,
                ':lbid' => $result['living_before_id'],
                ':pwd' => $this->data['password'],
                ':lmid' => $this->data['living_master_id'],
                ':rno' => $this->data['room_no'],
            ])->execute();

            $res_update = \Yii::$app->db->createCommand($UpdateSql,[
                ':lid' => $living_id,
                ':lbid' => $result['living_before_id'],
                ':lmid' => $this->data['living_master_id'],
                ':rem1' => (string)time(),
            ])->execute();
            if($res_update <= 0){
                $error = '直播记录7写入失败';
                \Yii::getLogger()->log('$error===:'.$error,Logger::LEVEL_ERROR);
                return false;
            }
        }
        elseif($this->data['living_type'] == 4)  //门票直播
        {
            $InsertSql = 'insert ignore into mb_living_passwrod_ticket (living_id,living_before_id,password,living_master_id,tickets,room_no) VALUES (:lid,:lbid,:pwd,:lmid,:tick,:rno)';
            $UpdateSql = 'update mb_living_passwrod_ticket set remark1=:rem1 where living_id=:lid and living_before_id=:lbid and living_master_id=:lmid';
            $res_insert = \Yii::$app->db->createCommand($InsertSql,[
                ':lid' => $living_id,
                ':lbid' => $result['living_before_id'],
                ':pwd' => $this->data['password'],
                ':lmid' => $this->data['living_master_id'],
                ':tick' => $this->data['tickets'],
                ':rno' => $this->data['room_no'],
            ])->execute();

            if($res_update <= 0){
                $error = '直播记录9写入失败';
                \Yii::getLogger()->log($error.'   sql===:'.\Yii::$app->db->createCommand($UpdateSql,[
                        ':lid' => $living_id,
                        ':lbid' => $result['living_before_id'],
                        ':pwd' => $this->data['password'],
                        ':lmid' => $this->data['living_master_id'],
                        ':rem1' => (string)time(),
                    ])->rawSql,Logger::LEVEL_ERROR);
                return false;
            }
        }
        $outInfo['living_id'] = $living_id;
        $outInfo['living_before_id'] = $result['living_before_id'];
        return true;

    }
}