<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Balance;
use frontend\business\BalanceUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;


/**
 * 用户送礼物虚拟豆处理
 */
class VirtualTicketMyMoneyTrans  implements ISaveForTransaction
{
    private  $params;
    private  $balance;

    /**
     * @param $banlances_object
     * @param $params
     */
    public function __construct($balance,$params)
    {
        $this->balance = $balance;
        $this->params = $params;
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

        $sql = 'update mb_balance set virtual_bean_balance=virtual_bean_balance-:gvalue ,send_ticket_count=send_ticket_count+:gvalue2,
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
                  where user_id=:uid and virtual_bean_balance>=:gvalue3';//虚拟
        $result = \Yii::$app->db->createCommand($sql,[
            ':gvalue' => $this->params['gift_value'],
            ':gvalue2' => $this->params['gift_value'],
            ':gvalue3' => $this->params['gift_value'],
            ':uid' => $this->params['user_id'],
        ])->execute();

        if($result <= 0){
            $error = '用户账户余额修改失败';
            \Yii::getLogger()->log('用户虚拟账户余额修改失败  '.
                \Yii::$app->db->createCommand($sql,[
                    ':gvalue' => $this->params['gift_value'],
                    ':gvalue2' => $this->params['gift_value'],
                    ':gvalue3' => $this->params['gift_value'],
                    ':uid' => $this->params['user_id'],
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }


}