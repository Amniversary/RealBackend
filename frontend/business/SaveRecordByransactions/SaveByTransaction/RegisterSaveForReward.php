<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/22
 * Time: 16:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\components\Des3Crypt;
use common\components\UsualFunForStringHelper;
use common\models\Balance;
use common\models\Client;
use common\models\ClientActive;
use common\models\ClientOther;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class RegisterSaveForReward implements ISaveForTransaction
{

    private $getClientRecord = null;
    private $extend_params=[];

    /**
     * 注册信息保存
     * @param $record
     * @param array $extend_params
     */
    public function __construct($record,$extend_params=[])
    {
        $this->getClientRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->getClientRecord instanceof Client))
        {
            $error = '不是用户对象';
            return false;
        }

        if(!$this->getClientRecord->save())
        {
            $error = '用户数据保存失败';
            \Yii::getLogger()->log('保存数据错误: '.var_export($this->getClientRecord->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        if($this->getClientRecord->register_type != 1)
        {
            if($this->getClientRecord->register_type == 2)
            {
                $sql = 'select user_id FROM mb_client_other where other_id = :od ';
                $user_id = \Yii::$app->db->createCommand($sql,[
                    ':od'=>$this->getClientRecord->unique_no
                ])->queryScalar();
                if(!empty($user_id))
                {
                    $error ='该微信号已绑定到其他账号，无法登录';
                    return false;
                }
                //微信绑定，更新用户微信绑定状态
                /*if(!ClientInfoUtil::UpdateWeixinBind($this->getClientRecord->client_id,$error))
                {
                    return false;
                }*/
            }
            $other = new ClientOther();
            $other->user_id = $this->getClientRecord->client_id;
            $other->other_id = $this->getClientRecord->unique_no;
            $other->register_type = $this->getClientRecord->register_type;
            $other->create_time = date('Y-m-d H:i:s');
            if(!$other->save())
            {
                $error = '第三方数据保存失败';
                \Yii::getLogger()->log('第三方数据错误: '.var_export($other->getErrors(),true),Logger::LEVEL_ERROR);
                return false;
            }


        }

        $active = new ClientActive();
        $active->user_id = $this->getClientRecord->client_id;
        $active->attention_num = 0;
        $active->funs_num = 0;
        $active->experience = 0;
        $active->level_no = 1;

        if(!$active->save())
        {
            $error = '账户活跃信息保存失败';
            \Yii::getLogger()->log('账户活跃信息数据错误: '.var_export($active->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        $srt = \Yii::$app->params['log_encrypt_key'];
        $pwd = UsualFunForStringHelper::mt_rand_str(6,'0123456789');
        $pwd = Des3Crypt::des_encrypt($pwd,$srt);
        $balance = new Balance();
        $balance->user_id = $this->getClientRecord->client_id;
        $balance->pay_pwd = $pwd;
        $balance->bean_balance = 0;
        $balance->virtual_bean_balance = 0;
        $balance->ticket_count = 0;
        $balance->ticket_real_sum = 0;
        $balance->ticket_count_sum = 0;
        $balance->virtual_ticket_count = 0;
        $balance->send_ticket_count = 0;
        $balance->rand_str = UsualFunForStringHelper::mt_rand_str(40);

        if(!$balance->save())
        {
            $error = '用户账户信息保存失败';
            \Yii::getLogger()->log('用户账户数据错误: '.var_export($balance->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        $sql = 'update mb_balance
                set sign=MD5(
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
                where user_id=:uid
                ';

        //\Yii::getLogger()->log('userID :'.$balance->user_id,Logger::LEVEL_ERROR);
        $rst = \Yii::$app->db->createCommand($sql,[
            ':uid'=>$balance->user_id
        ])->execute();

        if($rst <= 0)
        {
            $error = '账户余额签名更新失败';
            \Yii::getLogger()->log($error.' sql:'.$sql,Logger::LEVEL_ERROR);
            throw new Exception($error);
        }
        $outInfo = $this->getClientRecord;
        return true;
    }
} 