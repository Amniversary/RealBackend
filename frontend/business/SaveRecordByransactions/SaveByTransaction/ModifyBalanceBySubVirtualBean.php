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
 * 修改扣除虚拟豆数
 * Class ModifyBalanceByTicketToCash
 * @package frontend\business\UserAccountBalanceActions
 */
class ModifyBalanceBySubVirtualBean implements ISaveForTransaction
{
    private  $balance = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * extend_params 需要的参数
     * vitrual_bean_num 虚拟豆数
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
        if(!isset($params['vitrual_bean_num']) || empty($params['vitrual_bean_num']) || doubleval($params['vitrual_bean_num'])<= 0)
        {
            $error = '豆数必须大于0';
            return false;
        }
        if(!BalanceUtil::CheckBalance($this->balance,$error))
        {
            return false;
        }
        $sql = 'update mb_balance set virtual_bean_balance = virtual_bean_balance - :am ,
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
where user_id=:uid and virtual_bean_balance >= :am1
';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':am'=>$params['vitrual_bean_num'],
            ':am1'=>$params['vitrual_bean_num'],
            ':uid'=>$this->balance->user_id
        ])->execute();
        if($rst <= 0)
        {
            $sqlError = \Yii::$app->db->createCommand($sql,[
                ':am'=>$params['vitrual_bean_num'],
                ':am1'=>$params['vitrual_bean_num'],
                ':uid'=>$this->balance->user_id
            ])->rawSql;
            $error = '扣除虚拟豆数失败';
            \Yii::getLogger()->log($error.'user_id:['.$this->balance->user_id.'] sql:'.$sqlError,Logger::LEVEL_ERROR);
            throw new Exception($error);
        }
        return true;
    }
} 