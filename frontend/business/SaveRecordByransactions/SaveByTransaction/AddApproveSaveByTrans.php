<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/7
 * Time: 10:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Approve;
use common\models\BusinessCheck;
use frontend\business\ClientUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class AddApproveSaveByTrans implements ISaveForTransaction
{
    private  $extend_params=[];

    /**
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($extend_params=[])
    {
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $sql = 'update mb_client set is_centification = 3 where client_id=:cid and is_centification=4';
        $rst = \Yii::$app->db->createCommand($sql,[':cid'=>$this->extend_params['client_id']])->execute();

        $user =  ClientUtil::GetClientById($this->extend_params['client_id']);

        if($rst['is_centification'] == 2)
        {
            $error = '用户已认证';
            \Yii::getLogger()->log('更新用户认证信息失败 sql===:'.\Yii::$app->db->createCommand($sql,[':cid'=>$this->extend_params['client_id']])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }
        if($rst['is_centification'] == 3)
        {
            $error = '该用户认证正在审核中';
            \Yii::getLogger()->log('更新用户认证信息失败 sql===:'.\Yii::$app->db->createCommand($sql,[':cid'=>$this->extend_params['client_id']])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }


        $InsertSql = 'insert ignore into mb_approve(actual_name,id_card,create_time,client_id,client_no) VALUES (:aname,:idcard,:ctime,:cd,:cn)';

        $sql = 'update mb_approve set actual_name=:aname,id_card=:idcard,create_time=:ctime,client_id=:cd,status=:status WHERE client_no=:cn';

        \Yii::$app->db->createCommand($InsertSql,[
            ':aname' => null,
            ':idcard' => null,
            ':ctime' => null,
            ':cd' => $this->extend_params['client_id'],
            ':cn' => $user['client_no'],
        ])->execute();

        $result = \Yii::$app->db->createCommand($sql,[
            ':aname' => $this->extend_params['actual_name'],
            ':idcard' => $this->extend_params['id_card'],
            ':ctime' => date('Y-m-d H:i:s',time()),
            ':cd' => $this->extend_params['client_id'],
            ':cn' => $user['client_no'],
            ':status' => 0,
        ])->execute();

        if($result <= 0){
            \Yii::getLogger()->log('sql=直播认证失败111111111',Logger::LEVEL_ERROR);
            \Yii::getLogger()->log('sql='.\Yii::$app->db->createCommand($InsertSql,[
                    ':aname' => null,
                    ':idcard' => null,
                    ':ctime' => null,
                    ':cd' => $this->extend_params['client_id'],
                    ':cn' => $user['client_no'],
                    ':pn' => $user['phone_no'],
                ])->rawSql,Logger::LEVEL_ERROR);
            $error = '直播认证失败1';
            return false;
        }

        $results = Approve::findOne(['client_no' => $user['client_no']]);

        $check_sql = 'insert into mb_business_check(relate_id,business_type,status,check_result_status,create_time,check_time,check_user_id,check_user_name,create_user_id,create_user_name,check_no,refused_reason)
values(:rid,3,0,0,:ctime,0,0,"",:cid,:cname,:cno,"")';
        $check_result = \Yii::$app->db->createCommand($check_sql,[
            ':rid' => $results['approve_id'],
            ':ctime' => date('Y-m-d H:i:s'),
            ':cid' => $this->extend_params['client_id'],
            ':cname' => $this->extend_params['nick_name'],
            ':cno' => $results['approve_id']%20
        ])->execute();

        if($check_result <= 0){
            \Yii::getLogger()->log('check_sql='.$check_sql,Logger::LEVEL_ERROR);
            $error = '直播认证失败2';
            return false;
        }

        return true;



    }
}