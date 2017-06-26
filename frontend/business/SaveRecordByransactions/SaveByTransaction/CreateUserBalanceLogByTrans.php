<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 15:26
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Balance;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use frontend\business\UserBalanceLogUtil;
use yii\base\Exception;
use yii\log\Logger;

class CreateUserBalanceLogByTrans implements ISaveForTransaction
{
    private  $balance = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params，
     * extend_params需要的参数
     * op_value 操作金额
     * operate_type 操作类型
     * unique_id 操作唯一码
     * device_type 设备类型
     * relate_id 相关记录id，可以为空
     * field 操作字段
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->balance = $record;
        $this->extend_params = $extend_params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)
    {

        $error = '';
        if(!($this->balance instanceof Balance))
        {
            $error = '不是用户账户对象，数据异常';
            return false;
        }
        $op_value = doubleval($this->extend_params['op_value']);
        if($op_value <= 0)
        {
            if(isset($outInfo['op_value']) && doubleval($outInfo['op_value']) > 0)
            {
                $op_value = doubleval($outInfo['op_value']);
            }
            else
            {
                $error = '操作金额必须大于零';
                return false;
            }
        }
        $operateType = $this->extend_params['operate_type'];
        $unique_id = $this->extend_params['unique_id'];
        $device_type = $this->extend_params['device_type'];

        $field = $this->extend_params['field'];
        $relate_id = $this->extend_params['relate_id'];
        if(empty($relate_id))
        {
            $relate_id = $outInfo['relate_id'];   //打赏表id
        }

        if (!UserBalanceLogUtil::CreateBalanceLog($this->balance,$device_type,$op_value,$operateType,$field,$error,$unique_id,$relate_id))
        {
            return false;
        }

        return true;
    }
} 