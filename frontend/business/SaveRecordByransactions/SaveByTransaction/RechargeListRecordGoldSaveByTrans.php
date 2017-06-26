<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/4
 * Time: 19:37
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\GoldsPrestore;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;

class RechargeListRecordGoldSaveByTrans implements ISaveForTransaction
{
    private  $recharge = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->recharge = $record;
        $this->extend_params = $extend_params;
    }
    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->recharge instanceof GoldsPrestore))
        {
            $error = '不是充值记录对象';
            return false;
        }
        $other_pay_bill = $this->extend_params['other_pay_bill'];
        if(empty($other_pay_bill))
        {
            $error = '第三方充值账单号为空';
            return false;
        }
        $sql = 'update mb_golds_prestore set other_pay_bill=:ord, status_result=2, remark1=:nn where prestore_id=:rid and status_result = 1';
        $rst =\Yii::$app->db->createCommand($sql,[
            ':ord'=>(string)$other_pay_bill,
            ':rid'=>$this->recharge->prestore_id,
            ':nn'=>$this->extend_params['user_name']
        ])->execute();
        if($rst <= 0)
        {
            $sql = \Yii::$app->db->createCommand($sql,[
                ':ord'=>(string)$other_pay_bill,
                ':rid'=>$this->recharge->prestore_id,
                ':nn'=>$this->extend_params['user_name']
            ])->rawSql;
            \Yii::getLogger()->log('充值状态更新失败，状态不正确，sql:'.$sql.'        $other_pay_bill='.$other_pay_bill.'    ---'.is_numeric($other_pay_bill), Logger::LEVEL_ERROR);
            throw new Exception('充值状态更新失败');
        }
        return true;
    }
} 