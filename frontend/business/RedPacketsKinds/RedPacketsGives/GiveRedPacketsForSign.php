<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/25
 * Time: 17:46
 */

namespace frontend\business\RedPacketsKinds\RedPacketsGives;

use frontend\business\BusinessLogUtil;
use frontend\business\FundUtil;
use frontend\business\MessageUtil;
use frontend\business\RedPacketsKinds\IRedPacketsGet;
use frontend\business\RedPacketsUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FundSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;
use yii\base\Exception;
use yii\log\Logger;

/**
 * 签到红包：发布规则：
签到每天发红包:
第1，2天奖励红包（0.1-1元随机），
第3天奖励额度（5元），
第4天奖励红包（0.1-1元随机），
第5,6天奖励额度（5元或者10元），
第7天奖励红包（1-5元）
红包第二天才能使用
打赏金额大于红包金额可以使用
 * Class GiveDirectRewardRedPackets
 * @package frontend\business\RedPacketsGives
 */
class GiveRedPacketsForSign implements IRedPacketsGet
{
    function GetRedPackets($params,&$error)
    {
        //奖励红包
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
        if($red_packets->packets_type !== 260)
        {
            $error = '红包状态错误，不是签到红包';
            return false;
        }
        if(!isset($params['sign_days']))
        {
            $error = '签到天数为空';
            return false;
        }
        $signDays = $params['sign_days'];
        $rand_value = 0.00;
        $hasFund = false;
        switch(intval($signDays))
        {
            case 1:
            case 2:
            case 4:
                $rand_value = rand(1,10) / 10.0;
                break;
            case 3:
                $rand_value = 5.0;
                $hasFund =true;
                break;
            case 5:
            case 6:
                $valueAry = [5.0,10.0];
                $randIndex = rand(0,1);
                $rand_value = $valueAry[$randIndex];
                $hasFund = true;
                break;
            case 7:
                $rand_value = rand(10,50) / 10.0;
                break;
            default:
                $error = '签到天数超过七天，参数错误';
                return false;
        }
        if($hasFund === true)
        {
            $fundInfo = FundUtil::GetFundByUserId($user->account_id);
            if(!isset($fundInfo))
            {
                $error = '美愿基金信息不存在';
                \Yii::getLogger()->log($error.' user_id:'.strval($user->account_id),Logger::LEVEL_ERROR);
                return false;
            }
            if(doubleval($fundInfo->credit_money) > (doubleval($fundInfo->credit_balance) + $rand_value))
            {
                $transActions = [];
                $fundInfo->credit_money += $rand_value;
                $fundInfo->credit_balance += $rand_value;
                $transActions[] = new FundSaveForReward($fundInfo);

                $businessLog = BusinessLogUtil::GetBusinessLogNew('128',$user);
                $businessLog->remark5 = strval($fundInfo->fund_id);
                //$businessLog->remark6 = $billRecord->bill_id;
                //$businessLog->remark7 = strval($billInfo->account_info_id);
                $businessLog->remark9 = sprintf('%s第【%s】天签到增加了美愿基金可用余额【%s】元，增加前额度上限【%s】，增加前可用余额【%s】',
                    $user->nick_name,
                    $signDays,
                    $rand_value,
                    $fundInfo->credit_money,
                    $fundInfo->credit_balance);
                $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'签到美愿基金可用额度增加业务日志存储异常']);

                $msgContent = sprintf('您第【%s】天签到增加了美愿基金可用额度【%s】',$signDays,$rand_value);
                $msg = MessageUtil::GetMsgNewModel('73',$msgContent,$user->account_id);
                $transActions[] = new MessageSaveForReward($msg);
                if(!RewardUtil::RewardSaveByTransaction($transActions, $error))
                {
                    return false;
                }
                //返回红包信息
                $error = sprintf('签到成功，恭喜获得%s元美愿额度！',$rand_value);
            }
            else
            {
                $error = '签到成功，美愿基金额度已满，请提升';
                \Yii::getLogger()->log(sprintf('美愿基金额金额超越上限，上限【%s】，可用余额【%s】，新增金额【%s】，user_id：【%s】',
                    $fundInfo->credit_money,$fundInfo->credit_balance,$rand_value,$user->account_id), Logger::LEVEL_ERROR);
            }

        }
        else
        {
            $personRedPackets = RedPacketsUtil::GetPersonalRedPacketsNewModel($red_packets, $user);
            //第二天才能使用
            $start_time = date('Y-m-d',strtotime($personRedPackets->start_time.' +1 days'));
            $personRedPackets->start_time = $start_time;
            $personRedPackets->end_time = date('Y-m-d',strtotime($personRedPackets->end_time.' +1 days'));
            $personRedPackets->packets_money =strval($rand_value);
            $msgContent = sprintf('您领到了第【%s】天的签到红包【%s】元',$signDays,$rand_value);
            $msg = MessageUtil::GetMsgNewModel('8',$msgContent,$user->account_id);

            $insertSql = 'insert into my_personal_red_packets(`user_id`,
`packets_title`,`discribtion`,`packets_money`,`pic`,`over_pic`,
`is_rand`,`packets_type`,`start_time`,`end_time`,`status`,
`is_base_verify`,`create_time`,`over_money_for_us`,`remark1`)
select :uid, :ptl ,:dec ,:pm,:pic,:opic,:isr,:pt,:st,:et,:sts,:ibv,:ctime,:omfu,:rk1 from my_personal_red_packets
where not EXISTS (select personal_packets_id from my_personal_red_packets where user_id=:muid and packets_type=260 and start_time=:starttime) limit 1
';

            $trans = \Yii::$app->db->beginTransaction();
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
                    ':muid'=>$user->account_id,
                    ':starttime'=>$start_time
                ])->execute();
                if($rst <= 0)
                {
                    \Yii::getLogger()->log('已经领取过今天的签到红包，user_id:'.strval($user->account_id),Logger::LEVEL_ERROR);
                    throw new Exception('已经领取过今天的签到红包');
                }
/*                if(!$personRedPackets->save())
                {
                    \Yii::getLogger()->log(var_export($personRedPackets->getErrors(), true),Logger::LEVEL_ERROR);
                    throw new Exception('红包信息保存失败');
                }*/
                if(!$msg->save())
                {
                    \Yii::getLogger()->log(var_export($msg->getErrors(), true),Logger::LEVEL_ERROR);
                    throw new Exception('消息信息保存失败');
                }
                $trans->commit();
                $error = sprintf('签到成功，恭喜获得%s元打赏红包！',$rand_value);
            }
            catch(Exception $e)
            {
                $error = $e->getMessage();
                $trans->rollBack();
                return false;
            }
        }

        return true;
    }
} 