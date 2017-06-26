<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Balance;
use frontend\business\BalanceUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;


/**
 * 主播收礼物实际豆处理
 */
class TicketLivingMasterMoneyTrans  implements ISaveForTransaction
{
    private  $balance;
    private  $params;

    /**
     * @param $banlances_object
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

        if(!BalanceUtil::CheckBalance($this->balance,$error))
        {
            return false;
        }

        if(!isset($this->params['lucky_gift_value']) || empty($this->params['lucky_gift_value']))
        {
            $this->params['lucky_gift_value'] = $this->params['gift_value'];
        }

        $sql = 'update mb_balance set ticket_count=ticket_count+:gvalue
,ticket_real_sum=ticket_real_sum+:gvalue2
,ticket_count_sum=ticket_count_sum+:gvalue3,
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
            ':gvalue' => $this->params['gift_value'],
            ':gvalue2' => $this->params['gift_value'],
            ':gvalue3' => $this->params['lucky_gift_value'],
            ':uid' => $this->params['living_master_id'],
        ])->execute();

        if($result <= 0){
            $error = '用户账户余额修改失败';
            \Yii::getLogger()->log('主播账户余额修改失败  '.
                \Yii::$app->db->createCommand($sql,[
                    ':gvalue' => $this->params['gift_value'],
                    ':gvalue2' => $this->params['gift_value'],
                    ':gvalue3' => $this->params['lucky_gift_value'],
                    ':uid' => $this->params['living_master_id'],
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }


}