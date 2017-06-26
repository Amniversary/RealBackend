<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/31
 * Time: 10:55
 */

namespace frontend\business\OtherPay\OtherPayResultKinds;


use frontend\business\BusinessLogUtil;
use frontend\business\ChatUtilHuanXin;
use frontend\business\MessageUtil;
use frontend\business\OtherPay\IOtherPayResult;
use frontend\business\PersonalNewStatisticUtil;
use frontend\business\PersonalUserUtil;
use frontend\business\RedPacketsUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FriendInfoSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\PersonalRedPacketsSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RewardListSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UserActiveSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\WishSaveForReward;
use frontend\business\UserActiveUtil;
use frontend\business\WishNewStatisticUtil;
use frontend\business\WishUtil;
use frontend\business\FriendsUtil;
use yii\log\Logger;

class LlpayOtherPayResultForReward implements IOtherPayResult
{
    public function DoOtherPayResult($params, &$error)
    {
        //{"bank_code":"03080000","dt_order":"20160316192210","info_order":"reward_id=9302&pay_target=reward","money_order":"0.01","no_order":"ZHF-15-12-308820","oid_partner":"201601071000671903","oid_paybill":"2016031642786699","pay_type":"D","result_pay":"SUCCESS","settle_date":"20160316","sign":"f2184849e980e28a66d68588a983e516","sign_type":"MD5"}
        $transActions = [];
        if($params['trade_ok'] !== '2')
        {
            //支付失败不做处理
            $error = '连连打赏支付失败，单号：'.$params['out_trade_no'];
            \Yii::getLogger()->log($error, Logger::LEVEL_ERROR);
            return false;
        }
        //获取支持记录、愿望记录、生成支持业务日志消息、更新活跃度信息
        if(!isset($params['reward_id']) || empty($params['reward_id']))
        {
            $error = '连连打赏记录id丢失';
            return false;
        }
        $reward_id = $params['reward_id'];
        $rewardInfo = RewardUtil::GetRewardInfoById($reward_id);
        if(!isset($rewardInfo))
        {
            $error = '连连打赏记录找不到';
            \Yii::getLogger()->log($error.' reward_id:'.$reward_id, Logger::LEVEL_ERROR);
            return false;
        }
        if($rewardInfo->pay_bill !== $params['out_trade_no'])
        {
            $error = '连连交易单号不一致';
            \Yii::getLogger()->log($error.' source:'.$rewardInfo->pay_bill. ' send:'.$params['out_trade_no'], Logger::LEVEL_ERROR);
            return false;
        }
        if($rewardInfo->pay_status === 2)
        {
            $error = '连连支付记录已经处理';
            \Yii::getLogger()->log($error.' reward_id:'.$reward_id, Logger::LEVEL_ERROR);
            return false;
        }
        $rewardInfo->other_pay_bill = $params['trade_no'];
        $rewardInfo->pay_status = 2;
        $transActions[] = new RewardListSaveForReward($rewardInfo,[]);

        $wish = WishUtil::GetWishRecordById($rewardInfo->wish_id);
        if(!isset($wish))
        {
            $error = '找不到愿望';
            return false;
        }
        if(!empty($rewardInfo->red_packets_id))
        {
            $redPacket = RedPacketsUtil::GetPersonalRedPacketsById($rewardInfo->red_packets_id);
            if(!isset($redPacket))
            {
                $rewardInfo->reward_money = $rewardInfo->reward_money_except_packets;
                $rewardInfo->red_packets_id = null;
                $rewardInfo->red_packets_money = 0.0;
            }
            else
            {
                if($redPacket->status !== 2)
                {
                    $rewardInfo->reward_money = $rewardInfo->reward_money_except_packets;
                    $rewardInfo->red_packets_id = null;
                    $rewardInfo->red_packets_money = 0.0;
                }
                else
                {
                    $redPacket->status = 1;//已使用
                    $transActions[] = new PersonalRedPacketsSaveForReward($redPacket);//将红包设置成已使用
                }
            }
        }
        $transActions[] = new WishSaveForReward($wish,['pay_left_money'=>$rewardInfo->reward_money_except_packets,'packetsMoney'=>!isset($rewardInfo->red_packets_money)?'0.0':$rewardInfo->red_packets_money]);

        $userActive = UserActiveUtil::GetUserActiveByUserId($rewardInfo->reward_user_id);
        if(!isset($userActive))
        {
            $error = '用户活跃度信息找不到';
            return false;
        }

        $transActions[] = new UserActiveSaveForReward($userActive,['reward_money'=>$rewardInfo->reward_money]);
        $friendsList = [];
        $wishPublishId = $wish->publish_user_id;
        $user_id = $rewardInfo->reward_user_id;
        $friend = FriendsUtil::GetFriendOne($wishPublishId, $user_id);
        if(!isset($friend) && $wishPublishId != $user_id)//自己不需要加自己为朋友
        {
            $friendModel = FriendsUtil::GetNewModel($wishPublishId, $user_id);
            $transActions[] = new FriendInfoSaveForReward($friendModel,[]);
            $friendsList[] = [strval($wishPublishId),strval($user_id)];
        }
        //互相成为朋友
        $friend = FriendsUtil::GetFriendOne($user_id, $wishPublishId);
        if(!isset($friend) && $wishPublishId != $user_id)//自己不需要加自己为朋友
        {
            $friendModel = FriendsUtil::GetNewModel($user_id,$wishPublishId);
            $transActions[] = new FriendInfoSaveForReward($friendModel,[]);
            $friendsList[] = [strval($user_id),strval($wishPublishId)];
        }

        //支持业务日志
        $businessLog = BusinessLogUtil::GetBusinessLogForAlipayReward($rewardInfo,$wish);
        $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'打赏业务日志']);


        //获取支持记录、愿望记录、生成支持业务日志消息、更新活跃度信息  通知愿望发起人消息
        $msgContent = sprintf('%s打赏了您的愿望【%s】',$rewardInfo->reward_user_name,$wish->wish_name);
        $msg  = MessageUtil::GetMsgNewModel(2,$msgContent,$wish->publish_user_id);

        $transActions[] = new MessageSaveForReward($msg,[]);


        if(!RewardUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(isset($user))
        {
            //第一次打赏时，双方领取红包
            if(PersonalUserUtil::IsFirstTimeReward($user,true))
            {
                if(!RedPacketsUtil::GetFirstRewardRedPackets($user,$wish,$rewardInfo->reward_money,$error))
                {
                    \Yii::getLogger()->log('llpay用户第一次打赏领取红包异常'.$error.' user_id:'.strval($user_id), Logger::LEVEL_ERROR);
                }
            }
        }
        else
        {
            \Yii::getLogger()->log('llpay用户不存在，不进行第一次打赏领取红包', Logger::LEVEL_ERROR);
        }
        //更新时间
        WishNewStatisticUtil::UpdateWishNewInfo($wish->wish_id);
        $cnt = sprintf('打赏了愿望【%s】',$wish->wish_name);
        PersonalNewStatisticUtil::UpdatePersonalNewInfo($wish,$user_id,$cnt,null,1);

        //环信加为好友
        foreach($friendsList as $friend)
        {
            if(!ChatUtilHuanXin::AddUserFriends($friend[0],$friend[1],$error))
            {
                \Yii::getLogger()->log('环信加为好友失败：'.$error,Logger::LEVEL_ERROR);
            }
        }
        return true;
    }
} 