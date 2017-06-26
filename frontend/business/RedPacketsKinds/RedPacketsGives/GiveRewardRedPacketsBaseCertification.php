<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/25
 * Time: 17:46
 */

namespace frontend\business\RedPacketsKinds\RedPacketsGives;

use frontend\business\BusinessLogUtil;
use frontend\business\MessageUtil;
use frontend\business\PersonalUserUtil;
use frontend\business\RedPacketsKinds\IRedPacketsGet;
use frontend\business\RedPacketsUtil;
use frontend\business\RewardUtil;
use frontend\business\WishUtil;
use yii\base\Exception;
use yii\db\Transaction;
use yii\log\Logger;

/**
 * 奖励愿望红包 1:2 5元封顶，打赏人初级认证就能用
 * Class GiveDirectRewardRedPackets
 * @package frontend\business\RedPacketsGives
 */
class GiveRewardRedPacketsBaseCertification implements IRedPacketsGet
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
        if($red_packets->packets_type !== 256)
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
        if(!isset($params['wish']))
        {
            $error = '愿望信息不能为空';
            return false;
        }
        $wish = $params['wish'];
        $wishUserId = $wish->publish_user_id;
        $wishUser = PersonalUserUtil::GetAccontInfoById($wishUserId);
        if(!isset($wishUser))
        {
            $error = '愿望发布人找不到';
            return false;
        }
        //获取第一次打赏的记录
        $rewardInfo = RewardUtil::GetFirstRewardInfo($wish->wish_id,$user->account_id);
        if(!isset($rewardInfo))
        {
            \Yii::getLogger()->log(sprintf('无法获取到第一次打赏记录，wish_id：%s，user_id：%s',$wish->wish_id,$user->account_id),Logger::LEVEL_ERROR);
            $error = '打赏记录无法获取';
            return false;
        }
        if(!empty($rewardInfo->first_red_packet_id))
        {
            $error = '已经领取过第一次打赏红包';
            return false;
        }
        $personRedPackets = RedPacketsUtil::GetPersonalRedPacketsNewModel($red_packets, $wishUser);
        $personRedPackets->packets_money = $pay_money;
        $packet_money = doubleval($personRedPackets->packets_money);
        $max_money = $red_packets->packets_money;// 5.0;//单倍
        if($max_money < $packet_money)
        {
            $personRedPackets->packets_money = strval($max_money);//1倍5元封顶
        }

        $wishHasModify = false;
        if($user->centification_level > 0)
        {
            $personRedPackets->status = '1';
            //$wish->red_packets_money = strval(doubleval($wish->red_packets_money) + doubleval($personRedPackets->packets_money));
            $msgContent = sprintf('您领到了奖励愿望红包【%s】元，已经加入您的愿望金额', $personRedPackets->packets_money);
            $wishHasModify =true;
        }
        else
        {
            $msgContent = sprintf('您领到了奖励愿望红包【%s】元，打赏人【%s】初级认证后才能加入您的愿望金额', $personRedPackets->packets_money,$user->nick_name);
        }

        $msg = MessageUtil::GetMsgNewModel('8',$msgContent,$wish->publish_user_id);
        $businessLog = BusinessLogUtil::GetBusinessLogForRedPacketsToWish($wish,$user,$personRedPackets);
        $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);
        try
        {
            if(!$personRedPackets->save())
            {
                \Yii::getLogger()->log(var_export($personRedPackets->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('红包信息保存失败');
            }
            $sql = '
            select reward_id from my_reward_list where reward_user_id=:wuid and first_red_packet_id > 0 and pay_status=2
            ';
            $firstRewardId = \Yii::$app->db->createCommand($sql,[
                ':wuid'=>$rewardInfo->reward_user_id,
            ])->queryScalar();
            if(intval($firstRewardId) > 0)
            {
                \Yii::getLogger()->log('已经领取过第一次红包，user_id：'.$rewardInfo->reward_user_id,Logger::LEVEL_ERROR);
                throw new Exception('已经领取过第一次打赏红包');
            }
            $sql = 'update my_reward_list set first_red_packet_id=:fid,first_red_packet_money=:fm where reward_id=:rid and first_red_packet_id is null;
            ';
            $rst = \Yii::$app->db->createCommand($sql,[
                ':fid'=>$personRedPackets->personal_packets_id,
                ':fm'=>$personRedPackets->packets_money,
                ':rid'=>$rewardInfo->reward_id
            ])->execute();
            if($rst <= 0)
            {
                throw new Exception('第一次打赏，愿望获取的红包已经领取');
            }
/*            $rewardInfo->first_red_packet_id = $personRedPackets->personal_packets_id;
            $rewardInfo->first_red_packet_money = $personRedPackets->packets_money;
            if(!$rewardInfo->save())
            {
                \Yii::getLogger()->log(var_export($rewardInfo->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('打赏记录第一次打赏红包设置失败');
            }*/
            if($wishHasModify)
            {
                if(!WishUtil::WishModify($wish,'red_packet_money',$error,['red_packet_money'=>$personRedPackets->packets_money]))
                {
                    \Yii::getLogger()->log(var_export($wish->getErrors(), true),Logger::LEVEL_ERROR);
                    throw new Exception('愿望信息保存失败');
                }

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