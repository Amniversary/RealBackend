<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/7
 * Time: 10:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class CheckApproveSaveByTrans implements ISaveForTransaction
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

        if($this->extend_params['approve_status'] == 1)
        {
            $sql = 'update mb_approve set status=:stat where approve_id=:aid';
            $result = \Yii::$app->db->createCommand($sql,[
                ':stat' => $this->extend_params['approve_status'],
                ':aid' => $this->extend_params['approve_id']
            ])->execute();
            //\Yii::getLogger()->log('aaaaaaaaaaaaaaaaaa___'.$result,Logger::LEVEL_ERROR);
            if($result <= 0){
                $error = '审核失败1';
                return false;
            }
        }


        $sql = 'update mb_business_check set status=1,check_result_status=:cstatus,check_time=:ctime,check_user_id=:cuid,check_user_name=:cuname,refused_reason=:reason where relate_id=:aid AND business_check_id= :bcid';
        $result = \Yii::$app->db->createCommand($sql,[
            ':cstatus' => $this->extend_params['check_result_status'],
            ':ctime' => date('Y-m-d H:i:s'),
            ':cuid' => $this->extend_params['check_user_id'],
            ':cuname' => $this->extend_params['check_user_name'],
            ':reason' => $this->extend_params['refuesd_reason'],
            ':aid' => $this->extend_params['approve_id'],
            ':bcid' => $this->extend_params['business_check_id'],
        ])->execute();
        if($result <= 0){
            $error = '审核失败2';
            return false;
        }

        if($this->extend_params['approve_status'] == 1)
        {
            if($this->extend_params['status'] == 4)
            {
                $client_status = 4;
            }
            else
            {
                $client_status = 2;
            }
        }
        else
        {
            if($this->extend_params['status'] == 4)
            {
                $client_status = 1;

            }
            else
            {
                $client_status = 4;
            }
        }
        $sql = 'update mb_client set is_centification=:is_check where client_id=:uid';
        $result = \Yii::$app->db->createCommand($sql,[
            ':is_check' => $client_status,
            ':uid' => intval($this->extend_params['create_user_id']),
        ])->execute();
        if($result <= 0){
            $error = '审核失败3';
            \Yii::getLogger()->log('params==='.var_export($this->extend_params,true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;

    }
}