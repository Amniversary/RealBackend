<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/25
 * Time: 15:49
 */

namespace frontend\business;

use common\components\UsualFunForStringHelper;
use common\models\Balance;
use common\models\Living;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddVirtualBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceBySubRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceBySubVirtualBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UpdateBalanceRecordTrans;
use yii\db\Query;
use yii\log\Logger;

/**
 * 账户余额业务类
 * Class BalanceUtil
 * @package frontend\business
 */
class BalanceUtil
{
    /**
     * 增加虚拟豆的修改方法
     * @param $user_id
     * @param $bean_num
     */
    public static function AddVirtualBeanNum($user_id,$bean_num,&$error)
    {
        $param = [
            'vitrual_bean_num'=>$bean_num,
            'operate_type'=>'14'
        ];
        $error = '';
        $userBalance = BalanceUtil::GetUserBalanceByUserId($user_id);

        if(!isset($userBalance))
        {
            $error = '账户记录不存在';
            return false;
        }

        $transActions = [];
        $transActions[] = new ModifyBalanceByAddVirtualBean($userBalance,$param);

        //修改余额记录表
        $transActions[] = new UpdateBalanceRecordTrans($userBalance,$param);
        $params = [
            'op_value'=>$bean_num,
            'operate_type'=>'14',
            'unique_id'=>UsualFunForStringHelper::CreateGUID(),
            'device_type'=>'3',
            'relate_id'=>'',
            'field'=>'virtual_bean_balance'
        ];
        $transActions[] = new CreateUserBalanceLogByTrans($userBalance,$params);
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }

    public static function SubReadBeanNum($user_id,$bean_num,&$error)
    {
        $param = [
            'bean_num'=>$bean_num,
            'operate_type'=>'20'
        ];
        $userBalance = BalanceUtil::GetUserBalanceByUserId($user_id);
        if(!isset($userBalance))
        {
            $error = '用户账户信息不存在';
            return false;
        }
        $transActions = [];
        $transActions[] = new ModifyBalanceBySubRealBean($userBalance,$param);

        //修改余额记录表
        $transActions[] = new UpdateBalanceRecordTrans($userBalance,$param);

        $params = [
            'op_value'=>$bean_num,
            'operate_type'=>'20',
            'unique_id'=>UsualFunForStringHelper::CreateGUID(),
            'device_type'=>'1',
            'relate_id'=>'',
            'field'=>'bean_balance'
        ];
        $transActions[] = new CreateUserBalanceLogByTrans($userBalance,$params);
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }


    /**
     * 增加实际豆修改方法
     * @param $user_id
     * @param $bean_num
     */
    public static function AddReadBeanNum($user_id,$bean_num,&$error)
    {
        $param = [
            'bean_num'=>$bean_num,
            'operate_type'=>'15',
        ];
        $userBalance = BalanceUtil::GetUserBalanceByUserId($user_id);
        if(!isset($userBalance))
        {
            $error = '用户账户信息不存在';
            return false;
        }
        $transActions = [];
        $transActions[] = new ModifyBalanceByAddRealBean($userBalance,$param);

        //修改余额记录表
        $transActions[] = new UpdateBalanceRecordTrans($userBalance,$param);

        $params = [
            'op_value'=>$bean_num,
            'operate_type'=>'15',
            'unique_id'=>UsualFunForStringHelper::CreateGUID(),
            'device_type'=>'1',
            'relate_id'=>'',
            'field'=>'bean_balance'
        ];
        //\Yii::getLogger()->log('parms:'.var_export($params,true),Logger::LEVEL_ERROR);
        $transActions[] = new CreateUserBalanceLogByTrans($userBalance,$params);
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 减少虚拟豆修改方法
     * @param $user_id
     * @param $vitrual_bean_num
     */
    public static function SubVirtualBeanNum($user_id,$vitrual_bean_num,&$error)
    {
        $param = [
            'vitrual_bean_num'=>$vitrual_bean_num,
            'operate_type'=>'16',
        ];
        $userBalance = BalanceUtil::GetUserBalanceByUserId($user_id);
        if(!isset($userBalance))
        {
            $error = '用户账户信息不存在';
            return false;
        }
        $transActions = [];
        $transActions[] = new ModifyBalanceBySubVirtualBean($userBalance,$param);

        //修改余额记录表
        $transActions[] = new UpdateBalanceRecordTrans($userBalance,$param);
        //送礼物
        $params = [
            'op_value'=>$vitrual_bean_num,
            'operate_type'=>'16',
            'unique_id' => UsualFunForStringHelper::CreateGUID(),
            'device_type'=>'3',
            'relate_id'=>'',
            'field'=>'virtual_bean_balance'
        ];
        $transActions[] = new CreateUserBalanceLogByTrans($userBalance,$params);
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }
    /**
     * 根据用户id获取余额信息
     * @param $user_id
     * @return Balance|null
     */
    public static function GetUserBalanceByUserId($user_id)
    {
        return Balance::findOne(['user_id'=>$user_id]);
    }

    /**
     * 验证账户是否正确
     * @param $balance
     * @param $error
     */
    public static function CheckBalance($balance,&$error)
    {
        if(!($balance instanceof Balance))
        {
            $error = '不是账户余额数据类型';
            return false;
        }
        $fileds = ['balance_id', 'user_id','pay_pwd', 'bean_balance','ticket_count', 'ticket_real_sum', 'ticket_count_sum', 'virtual_ticket_count', 'send_ticket_count', 'rand_str'];
        $numberFields = ['bean_balance','ticket_count', 'ticket_real_sum', 'ticket_count_sum', 'virtual_ticket_count', 'send_ticket_count'];

        $len = count($fileds);
        $sourceStr = '';
        for($i=0; $i <$len; $i++)
        {
            if(in_array($fileds[$i], $numberFields))
            {
                $sourceStr .= sprintf("$fileds[$i]=%0.2f&",$balance->$fileds[$i]);
            }
            else
            {
                $sourceStr .= sprintf("$fileds[$i]=%s&",$balance->$fileds[$i]);
            }
        }
        $sourceStr .= 'chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq';
        if(!($balance->sign === md5($sourceStr)))
        {
            \Yii::getLogger()->log('sign:'.$balance->sign,Logger::LEVEL_ERROR);
            \Yii::getLogger()->log('md5:'.md5($sourceStr),Logger::LEVEL_ERROR);
            $error = '账户信息异常，请与客服人员联系';
            return false;
        }
        return true;
    }


    /**
     * 根据用户id 获取用户个人信息和余额信息
     * @param $user_id
     * @return array
     */
    public static function GetUserBalanceById($user_id)
    {
        $query = (new Query())
            ->select(['client_id','client_no','nick_name','pic','ticket_count'])
            ->from('mb_client bc')
            ->innerJoin('mb_balance bb','bc.client_id=bb.user_id')
            ->where('client_id = :cd',[':cd'=>$user_id])
            ->all();

        $test =[];
        foreach($query as $list)
        {
            $test = $list;
        }

        return $test;
    }

    /**
     * 增加用户可提现的剩余票数
     * @param $client_id
     * @param $ticket_num
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function AddUserTicketNum($client_id,$ticket_num,&$error)
    {
        $userBalance = BalanceUtil::GetUserBalanceByUserId($client_id);
        $ticket_count = $userBalance['ticket_count'] + $ticket_num;
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
        where user_id=:uid';

        $rst = \Yii::$app->db->createCommand($sql,[
            ':gvalue' => $ticket_count,
            ':gvalue2' => $ticket_count,
            ':gvalue3' => $ticket_count,
            ':uid' => $client_id,
        ])->execute();

        if($rst <= 0)
        {
            return false;
        }

        $params = [
            'op_value'=>$ticket_num,
            'operate_type'=>'30',
            'unique_id'=>UsualFunForStringHelper::CreateGUID(),
            'device_type'=>'3',
            'relate_id'=>'',
            'field'=>'ticket_count'
        ];

        //修改余额记录表
        $transActions[] = new UpdateBalanceRecordTrans($userBalance,$params);

        $transActions[] = new CreateUserBalanceLogByTrans($userBalance,$params);
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 减少用户可提现的剩余票数
     * @param $client_id
     * @param $ticket_num
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SubUserTicketNum($client_id,$ticket_num,&$error)
    {
        $userBalance = BalanceUtil::GetUserBalanceByUserId($client_id);
        $ticket_count = $userBalance['ticket_count'] - $ticket_num;

        if($ticket_count < 0)
        {
            $error = '用户的可提现票数不能小于0';
            return false;
        }

        $sql = 'update mb_balance set ticket_count = :tc,
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
        where user_id=:uid';

        $rst = \Yii::$app->db->createCommand($sql,[
            ':uid'=>$client_id,
            ':tc'=>$ticket_count
        ])->execute();

        if($rst <= 0)
        {
            return false;
        }

        $params = [
            'op_value'=>$ticket_num,
            'operate_type'=>'31',
            'unique_id'=>UsualFunForStringHelper::CreateGUID(),
            'device_type'=>'3',
            'relate_id'=>'',
            'field'=>'ticket_count'
        ];

        $transActions[] = new UpdateBalanceRecordTrans($userBalance,$params);

        //先日志表中插入记录
        $transActions[] = new CreateUserBalanceLogByTrans($userBalance,$params);
        if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }

        return true;
    }

    /**
     * 根据用户id清除用户的剩余可提现票数
     * @param $user_id
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function DeleteUserTicketCount($user_id)
    {
        $sql = 'update mb_balance set ticket_count = 0,
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
        where user_id=:uid';

        $rst = \Yii::$app->db->createCommand($sql,[
            ':uid'=>$user_id
        ])->execute();

        if($rst <= 0)
        {
            return false;
        }

        return true;
    }
}