<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\components\UsualFunForStringHelper;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\db\Query;
use yii\log\Logger;

class QiNiuUpdateLivingSaveByTrans implements ISaveForTransaction
{
    private  $data=[];

    /**
     * @param $data   所要修改和插入的数据
     * @throws Exception
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $guid = UsualFunForStringHelper::CreateGUID();
        $sql = 'update mb_living set `create_time`=if(status=2,create_time,:time),remark3=:rm3,`status`=2,living_pic_url=:lpu,`push_type`=:ptype,`push_url`=:url,`pull_http_url`=:hturl,`pull_rtmp_url`=:rurl,`pull_hls_url`=:hlurl,`is_to_expirence`=:ce
         where living_id=:id';
        $this->data['data']['push_type']='0';


        //\Yii::getLogger()->log('更新直播状态为正在直播，living_id:'.$this->data['data']['living_id'].' create_time:'.date('Y-m-d H:i:s',time()).' living_master_id:'.$this->data['data']['room_master_id'],Logger::LEVEL_ERROR);
        $result = \Yii::$app->db->createCommand($sql,[
            ':time' => date('Y-m-d H:i:s',time()),
            ':ptype' => '0',
            ':url' => $this->data['data']['push_url'],
            ':hturl' => $this->data['data']['pull_http_url'],
            ':rurl' => $this->data['data']['pull_rtmp_url'],
            ':hlurl' => $this->data['data']['pull_hls_url'],
            ':lpu' => $this->data['data']['living_pic_url'],
            ':ce' => 0,
            ':id' => $this->data['data']['living_id'],
            ':rm3'=>$guid
        ])->execute();



        if($result <= 0){
            \Yii::getLogger()->log('update_sql='.\Yii::$app->db->createCommand($sql,[
                    ':time' => date('Y-m-d H:i:s',time()),
                    ':ptype' => $this->data['data']['push_type'],
                    ':url' => $this->data['data']['push_url'],
                    ':hturl' => $this->data['data']['pull_http_url'],
                    ':rurl' => $this->data['data']['pull_rtmp_url'],
                    ':hlurl' => $this->data['data']['pull_hls_url'],
                    ':ce' => 0,
                    ':id' => $this->data['data']['living_id'],
                    ':rm3'=>$guid
                ])->rawSql,Logger::LEVEL_ERROR);
            $error = '完善直播记录1更新失败';
            return false;
        }

        $InsertSql = 'insert ignore into mb_chat_room (living_id,room_master_id,create_time,manager_num,cur_manager_num,other_id,status,public,approval)
VALUES (:lid,:cmid,:ctime,:num,:mnum,:oid,:tag,:pub,:app)';

        $UpdateSql = 'update mb_chat_room set create_time=:ctime,manager_num=6,status=1,public=1,approval=1,remark1=:rm1,other_id=:oid where living_id=:lid';

        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':lid' =>$this->data['data']['living_id'],
            ':cmid' =>$this->data['data']['room_master_id'],
            ':ctime' =>date('Y-m-d H:i:s',time()),
            ':num' =>6,
            ':mnum' =>0,
            ':oid' =>$this->data['data']['group_id'],
            ':tag' =>1,
            ':pub' =>1,
            ':app' =>1,
        ])->execute();

        $res_update = \Yii::$app->db->createCommand($UpdateSql,[
            ':ctime' => date('Y-m-d H:i:s',time()),
            ':lid' => $this->data['data']['living_id'],
            ':oid' =>$this->data['data']['group_id'],
            ':rm1'=>$guid
        ])->execute();


        //不做判断，可能第二次更新
        if($res_update <= 0)
        {
            $error = '直播记录22更新失败';
            return false;
        }

//        $sql = 'SELECT LAST_INSERT_ID()';


        $query = new Query();
        $result = $query->select(['room_id','other_id'])->from('mb_chat_room')->where('living_id=:lid',[
            ':lid'=>$this->data['data']['living_id']
        ])->one();
        $group_id = $result['room_id'];
        $other_id = $result['other_id'];
        if(intval($group_id) > 0)
        {
            $sql = 'insert ignore into mb_chat_room_member (owner,user_id,group_id,hide_msg,create_time,modify_time,status)
VALUES (:owner,:uid,:gid,:msg,:ctime,:mtime,:tag)';
            $UpdateSql = 'update mb_chat_room_member set owner=:owner,hide_msg=:hmsg,create_time=:ctime,modify_time=:mtime,status=:tag,remark3=:rm3 where user_id=:uid and group_id=:gid';

            $result = \Yii::$app->db->createCommand($sql,[
                ':owner' =>1,
                ':uid' =>$this->data['data']['room_master_id'],
                ':gid' =>$group_id,
                ':msg' =>1,
                ':ctime' => null,
                ':mtime' => null,
                ':tag' =>1,
            ])->execute();

            $up_result = \Yii::$app->db->createCommand($UpdateSql,[
                ':owner' =>1,
                ':uid' =>$this->data['data']['room_master_id'],
                ':gid' =>$group_id,
                ':hmsg' =>1,
                ':ctime' => time(),
                ':mtime' => time(),
                ':tag' =>1,
                ':rm3'=>$guid
            ])->execute();

//            if($result === false){
//                $error = '直播记录3更新失败';
//                return false;
//            }
//
            if($up_result <= 0){
                \Yii::getLogger()->log('sql='.\Yii::$app->db->createCommand($UpdateSql,[
                        ':owner' =>1,
                        ':uid' =>$this->data['data']['room_master_id'],
                        ':gid' =>$group_id,
                        ':hmsg' =>1,
                        ':ctime' => time(),
                        ':mtime' => time(),
                        ':tag' =>1,
                    ])->rawSql,Logger::LEVEL_ERROR);
                $error = '直播记录3更新失败';
                return false;
            }


        }
        //$outInfo['group_id']=$other_id;

        return true;

    }
}