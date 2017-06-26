<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/12
 * Time: 20:57
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\ClientInfoUtil;
use frontend\business\ClientUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class UpdateClientInfoSaveByTrans implements ISaveForTransaction
{
    private $getClientRecord = null;
    private $extend_params=[];

    /**
     * 交换用户账号
     * @param $record
     * @param array $extend_params
     */
    public function __construct($record,$extend_params=[])
    {
        $this->getClientRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $client_id = $this->getClientRecord->client_id;
        $swop_id = $this->extend_params['swop_id'];

        $one = ClientUtil::GetClientOtherByUserId($client_id);
        $two = ClientUtil::GetClientOtherByUserId($swop_id);
        $one_alipay = ClientInfoUtil::GetAlipayBindInfo($client_id);
        $two_alipay = ClientInfoUtil::GetAlipayBindInfo($swop_id);

        if($client_id == -1 || $swop_id == -1 || $client_id == -2 || $client_id == -3 || $swop_id == -2 || $swop_id == -3)
        {
            $error = '用户ID不能为系统管理员';
            return false;
        }

        $sql = 'UPDATE mb_client SET client_id = -999 WHERE client_id = :cd';// id = -999    用户2
        $query = \Yii::$app->db->createCommand($sql,[
            ':cd'=>$client_id,
        ])->execute();
        if($query <= 0)
        {
            $error = '更新用户ID错误:1';
            return false;
        }

        $sql = 'UPDATE mb_client SET client_id = :cd WHERE client_id = :sd';// id = 2   用户3
        $swop_rst = \Yii::$app->db->createCommand($sql,[
            ':cd'=>$client_id,
            ':sd'=>$swop_id,
        ])->execute();
        if($swop_rst <= 0)
        {
            $error = '更新用户ID错误:2';
            return false;
        }

        $sql = 'UPDATE mb_client SET client_id = :sd WHERE client_id = -999';// id = 3    用户2
        $client_rst = \Yii::$app->db->createCommand($sql,[
            ':sd'=>$swop_id
        ])->execute();
        if($client_rst <= 0)
        {
            $error = '更新用户ID错误:3';
            return false;
        }


        if(!empty($one))
        {
            foreach($one as $o)
            {
                $record_ids[] = $o->record_id;
            }
            $ids = implode(",",$record_ids);
                $sql = 'UPDATE mb_client_other SET user_id = :sd WHERE find_in_set(record_id,:rd) ';
                $other_rst = \Yii::$app->db->createCommand($sql,[
                    ':sd'=>$swop_id,
                    ':rd'=>$ids
                ])->execute();
                if($other_rst <= 0)
                {
                    $error = '更新用户第三方ID错误1';
                    return false;
                }
        }

        if(!empty($two))
        {
            foreach($two as $t)
            {
                $record_Ids[] = $t->record_id;
            }
            $ids = implode(",",$record_Ids);
            $sql = 'UPDATE mb_client_other SET user_id = :cd WHERE find_in_set(record_id,:yd)';
            $y_other_rst = \Yii::$app->db->createCommand($sql,[
                ':cd'=>$client_id,
                ':yd'=>$ids
            ])->execute();
            if($y_other_rst <= 0)
            {
                $error = '更新用户第三方ID错误2';
                return false;
            }
        }

        if(!empty($one_alipay))
        {
            foreach($one_alipay as $o)
            {
                $alipay_id[] = $o->record_id;
            }
            $pd = implode(",",$alipay_id);
            $sql = 'UPDATE mb_alipay_for_cash SET user_id = :ad WHERE find_in_set(record_id,:ld)';
            $a_alipay_rst = \Yii::$app->db->createCommand($sql,[
                ':ad'=>$swop_id,
                ':ld'=>$pd,
            ])->execute();
            if($a_alipay_rst <= 0)
            {
                $error = '更新支付宝绑定账号错误:1';
                return false;
            }
        }

        if(!empty($two_alipay))
        {
            foreach($two_alipay as $two)
            {
                $alipay_ids[] = $two->record_id;
            }
            $ld = implode(",",$alipay_ids);
            $sql = 'UPDATE mb_alipay_for_cash SET user_id = :bd WHERE find_in_set(record_id,:dd)';
            $b_alipay_rst = \Yii::$app->db->createCommand($sql,[
                ':bd'=>$client_id,
                ':dd'=>$ld,
            ])->execute();
            if($b_alipay_rst <= 0)
            {
                $error = '更新支付宝绑定账号错误:2';
                return false;
            }
        }

        return true;
    }
} 