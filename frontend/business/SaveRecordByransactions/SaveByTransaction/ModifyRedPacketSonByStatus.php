<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/7
 * Time: 14:08
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Balance;
use common\models\RedPacketSon;
use frontend\business\BalanceUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

/**
 * 修改红包子表，领取状态和领取用户和领取时间，领红包和退红包都可以用
 * Class ModifyBalanceByTicketToCash
 * @package frontend\business\UserAccountBalanceActions
 */
class ModifyRedPacketSonByStatus implements ISaveForTransaction
{
    //private  $red_packet_son = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params gu_id 红包id   client_id 领取红包人id或退还人id  status状态：1已领取或2退还    red_packet_no：序号
     * @throws Exception
     */
    public function __construct($extend_params=[])
    {
        //$this->red_packet_son = $record;
        $this->extend_params = $extend_params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)//($params,&$balance, &$error)
    {
        $params = $this->extend_params;
        if(empty($params['gu_id'])|| empty($params['client_id']) || empty($params['status']) || doubleval($params['red_packet_no'])<= 0)
        {
            $error = '修改红包子表参数错误';
            \Yii::getLogger()->log('params:'.var_export($params,true),Logger::LEVEL_ERROR);
            return false;
        }
        if(!in_array(intval($params['status']),[1,2]))
        {
            $error = '处理红包状态错误';
            return false;
        }
        if($params['status'] == '2')
        {
            $sql = 'select red_packet_money,record_id from mb_red_packet_son where  gu_id=:gid and red_packet_no=:rpn for UPDATE ';
            $sql_params=[
                ':rpn'=>$params['red_packet_no'],
                ':gid'=>$params['gu_id']
            ];
        }
        else
        {
            $sql = 'select red_packet_money,record_id,luck from mb_red_packet_son where  gu_id=:gid and red_packet_no=:rpn and
not EXISTS (select record_id from mb_red_packet_son where gu_id=:gid1 and client_id=:cid )for UPDATE ';
            $sql_params=[
                ':rpn'=>$params['red_packet_no'],
                ':gid'=>$params['gu_id'],
                ':gid1'=>$params['gu_id'],
                ':cid'=>$params['client_id']
            ];
        }

        $info = \Yii::$app->db->createCommand($sql,$sql_params)->queryOne();
        if($info === false)
        {
            $error = '红包已发完';
            throw new Exception($error);
        }
        $money = $info['red_packet_money'];
        $rid = $info['record_id'];
        $sql = 'update mb_red_packet_son set status=:stu ,client_id=:cid,get_time=:gtime where record_id=:rid and status = 0';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':stu'=>$params['status'],
            ':cid'=>$params['client_id'],
            ':gtime'=>date('Y-m-d H:i:s'),
            ':rid'=>$rid
        ])->execute();
        if($rst <= 0)
        {
            $error = '红包已发完，下次速度快点哦';
            //\Yii::getLogger()->log($error.'user_id:['.$this->balance->user_id.'] sql:'.$sql,Logger::LEVEL_ERROR);
            throw new Exception($error);
        }
        if(!isset($outInfo) || !is_array($outInfo))
        {
            $outInfo= [];
        }
        $outInfo['op_value'] = $money;//返回金额
        $outInfo['lucky'] = $info['luck'];
        return true;
    }
} 