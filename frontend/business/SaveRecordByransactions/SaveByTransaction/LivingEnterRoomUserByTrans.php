<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/1
 * Time: 16:52
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\LivingPasswrodTicket;
use common\models\LivingPrivate;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class LivingEnterRoomUserByTrans implements ISaveForTransaction
{
    private $EnterRecord = null;
    private $extend_params =[];

    public function __construct($record,$extend_params=[])
    {
        $this->EnterRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if($this->EnterRecord['living_type'] == 4)
        {
            $living_password_info = LivingPasswrodTicket::findOne(['room_no' => $this->EnterRecord['room_no']]);
            if(!isset($living_password_info) || empty($living_password_info))
            {
                $error = '门票直播信息不存在';
                return false;
            }
            $insert_sql = 'insert ignore into mb_living_passwrod_ticket_views(tikcet_id,user_id) VALUES';
            $insert_sql .= sprintf('(%d,%d)',
                    $living_password_info->tikcet_id,
                    $this->EnterRecord['user_id']
                );
            \Yii::$app->db->createCommand($insert_sql)->execute();

            $update_sql = 'update mb_living_passwrod_ticket_views set remark1=:rem1 WHERE tikcet_id=:tid and user_id=:uid';
            $update_res = \Yii::$app->db->createCommand($update_sql,[
                ':rem1' => time(),
                ':tid' => $living_password_info->tikcet_id,
                ':uid' => $this->EnterRecord['user_id'],
            ])->execute();
            if($update_res <= 0)
            {
                $error = '门票观众信息写入失败';
                \Yii::getLogger()->log($error.'  sql ==:'.\Yii::$app->db->createCommand($update_sql,[
                        ':rem1' => time(),
                        ':tid' => $living_password_info->tikcet_id,
                        ':uid' => $this->EnterRecord['user_id'],
                    ])->rawSql,Logger::LEVEL_ERROR);
                return false;
            }

        }
        else
        {
            $living_private_info = LivingPrivate::findOne(['room_no' => $this->EnterRecord['room_no']]);
            if(!isset($living_private_info) || empty($living_private_info))
            {
                $error = '直播信息不存在';
                return false;
            }
            $InsertSql = 'insert ignore into mb_living_private_views (private_id,user_id) VALUES';
            $InsertSql .= sprintf('(%d,%d)',$living_private_info->private_id,$this->EnterRecord['user_id']);
            \Yii::$app->db->createCommand($InsertSql)->execute();

        }

        return true;
    }
} 