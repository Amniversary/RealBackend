<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/7
 * Time: 14:08
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Balance;
use frontend\business\BalanceUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

/**
 * 修改红包主表，数量和状态字段，领红包和退红包都可以用
 * Class ModifyBalanceByTicketToCash
 * @package frontend\business\UserAccountBalanceActions
 */
class ModifyRedPacketMainByNum implements ISaveForTransaction
{
    //private  $red_packet_main = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params gu_id 红包id
     * @throws Exception
     */
    public function __construct($extend_params=[])
    {
        //$this->balance = $record;
        $this->extend_params = $extend_params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)//($params,&$balance, &$error)
    {
        $params = $this->extend_params;
        if(empty($params['gu_id']))
        {
            $error = '红包id不能为空';
            return false;
        }
        $sql = 'update  mb_red_packet_main set get_num = get_num + 1,status=if(get_num = red_packet_num,0,1) where gu_id=:gid and status = 1';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':gid'=>$params['gu_id']
        ])->execute();
        if($rst <= 0)
        {
            $error = '修改红包数量失败，红包已发完';
            throw new Exception($error);
        }
        return true;
    }
} 