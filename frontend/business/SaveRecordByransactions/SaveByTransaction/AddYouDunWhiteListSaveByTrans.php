<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/17
 * Time: 18:28
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\OffUserLiving;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

/**
 * 有盾白名单
 * Class AddYouDunWhiteListSaveByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class AddYouDunWhiteListSaveByTrans implements ISaveForTransaction
{
    private  $extend_params=[];

    /**
     * @param array $extend_params
     * @throws // Exception
     */
    public function __construct($extend_params=[])
    {
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $date = date('Y-m-d H:i:s');
        $sql = 'update mb_client set is_centification = 3 where client_id=:cid';
        $rst = \Yii::$app->db->createCommand($sql,[':cid'=>$this->extend_params['client_id']])->execute();
        if($rst <= 0) {
            $error = '认证失败';
            \Yii::error('更新用户人脸识别认证信息失败 sql===:'.\Yii::$app->db->createCommand($sql,[':cid'=>$this->extend_params['client_id']])->rawSql,Logger::L);
            return false;
        }
        $InsertSql = 'insert ignore into mb_approve(actual_name,id_card,client_id) VALUES (:aname,:idcard,:cid)';

        $update_sql = 'update mb_approve set actual_name=:aname,id_card=:idcard,create_time=:ctime,status=:status,remark1 = :rma,remark2=:time WHERE client_id=:cid';

        \Yii::$app->db->createCommand($InsertSql,[
            ':aname' => '',
            ':idcard' => '',
            ':cid' => $this->extend_params['client_id'],
        ])->execute();

        $result = \Yii::$app->db->createCommand($update_sql,[
            ':aname' => $this->extend_params['actual_name'],
            ':idcard' => $this->extend_params['id_card'],
            ':ctime' => date('Y-m-d H:i:s',time()),
            ':cid' => $this->extend_params['client_id'],
            ':status' => 1,
            ':rma'=> $this->extend_params['result'],
            ':time'=> $date
        ])->execute();

        if($result <= 0){
            $error = '人脸认证失败';
            \Yii::error($error.':  sql='.\Yii::$app->db->createCommand($update_sql,[
                    ':aname' => $this->extend_params['actual_name'],
                    ':idcard' => $this->extend_params['id_card'],
                    ':ctime' => date('Y-m-d H:i:s',time()),
                    ':cid' => $this->extend_params['client_id'],
                    ':status' => 1,
                    ':rma'=>$this->extend_params['result'],
                    ':time'=> $date
                ])->rawSql);
            return false;
        }

        $off_living = new OffUserLiving();
        $off_living->client_no = $this->extend_params['client_no'];
        $off_living->remark1 = date('Y-m-d H:i:s');
        if(!$off_living->save())
        {
            $error = '保存白名单用户失败';
            \Yii::error($error.' '.var_export($off_living->getErrors(),true));
            return false;
        }
        return true;



    }
} 