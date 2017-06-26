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
 * 票提现
 * Class ModifyBalanceByTicketToCash
 * @package frontend\business\UserAccountBalanceActions
 */
class ModifyBalanceByTicketToCash implements ISaveForTransaction
{
    private  $balance = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->balance = $record;
        $this->extend_params = $extend_params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)//($params,&$balance, &$error)
    {
        if(!($this->balance instanceof Balance))
        {
            $error = '不是用户账户余额记录';
            return false;
        }
        $params = $this->extend_params;
        if(!isset($params['ticket_num']) || empty($params['ticket_num']) || doubleval($params['ticket_num'])<= 0)
        {
            $error = '提现票数必须大于0';
            return false;
        }
        if(!BalanceUtil::CheckBalance($this->balance,$error))
        {
            return false;
        }
        $sql = 'update mb_balance set ticket_count = ticket_count - :am ,
                sign=MD5(
                  CONCAT(
                  \'balance_id=\',balance_id,
                  \'&user_id=\',user_id,
                  \'&pay_pwd=\',pay_pwd,
                  \'&bean_balance=\', REPLACE(FORMAT(bean_balance,2),\',\',\'\'),
                  \'&ticket_count=\',REPLACE(FORMAT(ticket_count,2),\',\',\'\'),
                  \'&ticket_real_sum=\',REPLACE(FORMAT(ticket_real_sum,2),\',\',\'\'),
                  \'&ticket_count_sum=\',REPLACE(FORMAT(ticket_count_sum,2),\',\',\'\'),
                  \'&virtual_ticket_count=\',REPLACE(FORMAT(virtual_ticket_count,2),\',\',\'\'),
                  \'&send_ticket_count=\',REPLACE(FORMAT(send_ticket_count,2),\',\',\'\'),
                  \'&rand_str=\',rand_str,\'&chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq\'))
where user_id=:uid and ticket_count >= :am1
';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':am'=>$params['ticket_num'],
            ':am1'=>$params['ticket_num'],
            ':uid'=>$this->balance->user_id
        ])->execute();
        if($rst <= 0)
        {
            $error = '余额不足，扣除票数失败';
            \Yii::getLogger()->log($error.'user_id:['.$this->balance->user_id.'] sql:'.$sql,Logger::LEVEL_ERROR);
            throw new Exception($error);
        }
        return true;
    }
} 