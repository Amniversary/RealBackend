<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Balance;
use frontend\business\BalanceUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use frontend\business\UserBalanceLogUtil;
use yii\log\Logger;


/**
 * 用户审核、付款失败，拒绝提现，退款
 */
class RefundMoneyByTrans  implements ISaveForTransaction
{
    private  $ticket_num;
    private  $balance;

    /**
     *
     * @param $user_id      用户id
     * @param $ticket_num   提现票数
     */
    public function __construct($balance,$ticket_num)
    {
        $this->balance = $balance;
        $this->ticket_num = $ticket_num;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->balance instanceof Balance))
        {
            $error = '不是用户账户余额记录';
            return false;
        }

        if(!BalanceUtil::CheckBalance($this->balance,$error))
        {
            return false;
        }

        $sql = 'update mb_balance set ticket_count=ticket_count+:gvalue,
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
 where user_id=:uid';//实际
        $result = \Yii::$app->db->createCommand($sql,[
            ':gvalue' => $this->ticket_num,
            ':uid' => $this->balance->user_id,
        ])->execute();

        if($result <= 0){
            $error = '用户退款余额更新失败';
            \Yii::getLogger()->log('用户退款余额更新失败   $balance=:'.var_export($this->balance,true).'   ticket_num=:'.$this->ticket_num,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }


}