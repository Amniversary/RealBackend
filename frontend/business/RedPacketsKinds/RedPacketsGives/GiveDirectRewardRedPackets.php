<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/25
 * Time: 17:46
 */

namespace frontend\business\RedPacketsKinds\RedPacketsGives;

use common\components\PhpLock;
use common\models\PersonalRedPackets;
use frontend\business\BusinessLogUtil;
use frontend\business\MessageUtil;
use frontend\business\PersonalUserUtil;
use frontend\business\RedPacketsKinds\IRedPacketsGet;
use frontend\business\RedPacketsUtil;
use frontend\business\UserAccountInfoUtil;
use yii\base\Exception;
use yii\db\Transaction;
use yii\log\Logger;

/**
 * 直接奖励红包 1比3，10元封顶，加入账户余额
 * Class GiveDirectRewardRedPackets
 * @package frontend\business\RedPacketsGives
 */
class GiveDirectRewardRedPackets implements IRedPacketsGet
{
    function GetRedPackets($params,&$error)
    {
        //愿望、打赏人、红包、消息、业务逻辑日志、账户余额
        if(!isset($params) || !is_array($params))
        {
            $error = '参数不正确';
            return false;
        }
        if(!isset($params['red_packet']))
        {
            $error = '没有红包对象';
            return false;
        }
        $red_packets = $params['red_packet'];
        if(!isset($params['user']))
        {
            $error = '用户参数不能为空';
            return false;
        }
        $user = $params['user'];
        $cur_date = date('Y-m-d');
        if($red_packets->get_type == 2 && $cur_date > $red_packets->end_time)
        {
            $error = '红包已经过期';
            return false;//红包已经过期
        }
        if($red_packets->packets_type !== 64)
        {
            $error = '红包状态错误';
            return false;
        }
        if(!isset($params['pay_money']))
        {
            $error ='支付金额丢失';
            return false;
        }
        $pay_money = doubleval($params['pay_money']);
        $billInfo = PersonalUserUtil::GetUserBillInfoByUserId($user->account_id);
        if(!isset($billInfo))
        {
            $error = '账户信息丢失';
            return false;
        }
        $personRedPackets = RedPacketsUtil::GetPersonalRedPacketsNewModel($red_packets, $user);
        $insertSql = 'insert into my_personal_red_packets(`user_id`,
`packets_title`,`discribtion`,`packets_money`,`pic`,`over_pic`,
`is_rand`,`packets_type`,`start_time`,`end_time`,`status`,
`is_base_verify`,`create_time`,`over_money_for_us`,`remark1`)
select :uid, :ptl ,:dec ,:pm,:pic,:opic,:isr,:pt,:st,:et,:sts,:ibv,:ctime,:omfu,:rk1 from my_personal_red_packets
where not EXISTS (select personal_packets_id from my_personal_red_packets where user_id=:muid and packets_type=64) limit 1
';
        $packet_money = doubleval($personRedPackets->packets_money);
        $max_money = $pay_money * 3;
        //$max_money = $pay_money ;
        if($max_money > $packet_money)
        {
            $max_money = $packet_money;
        }
        $personRedPackets->packets_money = strval($max_money);//3倍奖励，封顶10元  改为1倍奖励 5元封顶
        $personRedPackets->status = '-1';//不显示，已使用
        $cont = sprintf('您收到了奖励红包【%s】元，已经加入您的账户余额', $personRedPackets->packets_money);
        $msg = MessageUtil::GetMsgNewModel('8',$cont,$user->account_id);
        //$billInfo->balance = sprintf('%0.2f',doubleval($billInfo->balance) + doubleval($personRedPackets->packets_money));
        $businessLog = BusinessLogUtil::GetBusinessLogForRedPackets($billInfo,$user,$personRedPackets);
        $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);
        try
        {
            $rst = \Yii::$app->db->createCommand($insertSql,[
              ':uid'=>$user->account_id,
              ':ptl'=>$personRedPackets->packets_title ,
              ':dec'=>$personRedPackets->discribtion ,
              ':pm'=>$personRedPackets->packets_money,
              ':pic'=>$personRedPackets->pic,
              ':opic'=>$personRedPackets->over_pic,
              ':isr'=>$personRedPackets->is_rand,
              ':pt'=>$personRedPackets->packets_type,
              ':st'=>$personRedPackets->start_time,
              ':et'=>$personRedPackets->end_time,
              ':sts'=>$personRedPackets->status,
              ':ibv'=>$personRedPackets->is_base_verify,
              ':ctime'=>$personRedPackets->create_time,
              ':omfu'=>$personRedPackets->over_money_for_us,
              ':rk1'=>$personRedPackets->remark1,
              ':muid'=>$user->account_id
            ])->execute();
            if($rst <= 0)
            {
                \Yii::getLogger()->log('已经领取过首次打赏红包返回余额红包11，user_id:'.strval($user->account_id),Logger::LEVEL_ERROR);
                throw new Exception('已经领取过首次打赏红包返回余额红包');
            }
/*            if(!$personRedPackets->save())
            {
                \Yii::getLogger()->log(var_export($personRedPackets->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('红包信息保存失败');
            }*/
            if(!UserAccountInfoUtil::ModifyUserBillAccountInfo('add_balance',['add_money'=>$personRedPackets->packets_money],$billInfo,$error))
            {
                throw new Exception($error);
            }
            if(!$msg->save())
            {
                \Yii::getLogger()->log(var_export($msg->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('消息信息保存失败');
            }
            if(!$businessLog->save())
            {
                \Yii::getLogger()->log(var_export($businessLog->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('业务日志信息保存失败');
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $trans->rollBack();
            return false;
        }
        return true;
    }
} 