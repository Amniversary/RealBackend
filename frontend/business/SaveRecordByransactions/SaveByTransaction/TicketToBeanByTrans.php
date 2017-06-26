<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Balance;
use frontend\business\BalanceUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;


/**
 * 用户票转豆
 */
class TicketToBeanByTrans  implements ISaveForTransaction
{
    private  $params;
    private  $balance;

    /**
     * @param $balance
     * @param $params
     */
    public function __construct($balance,$params)
    {
        $this->params = $params;
        $this->balance = $balance;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->balance instanceof Balance))
        {
            $error = '不是用户账户余额记录';
            return false;
        }

        if(!isset($this->params['bean_num']) || empty($this->params['bean_num']) || doubleval($this->params['bean_num'])<= 0)
        {
            $error = '豆数必须大于0';
            return false;
        }

        if(!BalanceUtil::CheckBalance($this->balance,$error))
        {
            return false;
        }

        $sql = 'update mb_balance set bean_balance=bean_balance+:bean_num ,ticket_count=ticket_count-:ticket_num,
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
 where user_id=:uid AND ticket_count >= :ticket_num';
        $result = \Yii::$app->db->createCommand($sql,[
            ':bean_num' => $this->params['bean_num'],
            ':ticket_num' => $this->params['ticket_num'],
            ':uid' => $this->params['user_id'],
        ])->execute();
        if($result <= 0){
            $error = '用户账户余额修改失败';
            \Yii::getLogger()->log('TicketToBeanByTrans--用户账户余额修改失败  '.
                \Yii::$app->db->createCommand($sql,[
                    ':bean_num' => $this->params['bean_num'],
                    ':ticket_num' => $this->params['ticket_num'],
                    ':uid' => $this->params['user_id'],
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }


}