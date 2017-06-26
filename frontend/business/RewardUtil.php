<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/16
 * Time: 9:22
 */

namespace frontend\business;
use common\components\Des3Crypt;
use common\components\WaterNumUtil;
use common\models\Reward;
use common\models\RewardList;
use common\components\PhpLock;
use common\models\UserActive;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BalanceSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BorrowFundSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\BusinessLogSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CheckRecordSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FriendInfoSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FundSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\LivingMasterRewardByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\MessageSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\PersonalRedPacketsSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RewardListSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\TicketLivingMasterMoneyTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\TicketMyMoneyTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UserActiveSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\VirtualTicketLivingMasterMoneyTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\VirtualTicketMyMoneyTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\WishSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\WishSaveForReward;
use yii\base\Exception;
use yii\db\Query;
use yii\db\Transaction;
use yii\log\Logger;
use common\models\BusinessLog;
use common\models\Message;
use common\models\BorrowFund;
use common\models\BusinessCheck;

class RewardUtil
{
    /**
     * 根据打赏ID获取打赏信息
     */
    public  static  function GetRewardById($reward_id)
    {
        return Reward::findOne(['reward_id'=>$reward_id]);
    }

    /**
     * 保存打赏信息
     * @param $reward
     * @param $error
     * @return bool
     */
    public static function SaveRewardInfo($reward,&$error)
    {
        if(!($reward instanceof RewardList))
        {
            $error = '不是评论对象';
            return false;
        }
        if(!$reward->save())
        {
            $error = '评论信息保存错误';
            \Yii::getLogger()->log($error.' :'.var_export($reward->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 获取用户第一次打赏记录
     * @param $user_id
     */
    public static function GetFirstRewardInfoByUserId($user_id)
    {
        return RewardList::findOne(['and',['reward_user_id'=>$user_id,'pay_status'=>'2'],'first_red_packet_id>0']);
    }

    /**
     * 获取一条打赏记录作为第一次打赏的记录，可以领取红包
     * @param $user_id
     */
    public static function GetRewardInfoForFirstReward($user_id)
    {
        return RewardList::findOne(['and',['reward_user_id'=>$user_id,'pay_status'=>'2'],'first_red_packet_id is null']);
    }

    /**
     * 获取第一次打赏记录
     * @param $wish_id
     * @param $user_id
     * @param $pay_type
     */
    public static function GetFirstRewardInfo($wish_id,$user_id)
    {
        return RewardList::find()->where(['wish_id'=>$wish_id,'reward_user_id'=>$user_id,'pay_status'=>'2'])->orderBy('reward_id desc')->one();
    }
    /**
     * 获取愿望所有的回报地址
     * @param $wish
     */
    public static function GetReturnAddressList($wish,$flag,$start_id)
    {
        if($wish->back_type === 1)//虚拟
        {
            $sql = 'select reward_id as id, reward_user_name as user_name,remark1 as pic,reward_money,\'\' as contract_user,\'\' as contract_call,\'\' as address, email, create_time as time from my_reward_list rl where rl.wish_id=:wid and  rl.pay_status=2';
        }
        else//实物
        {
            $sql = 'select reward_id as id, reward_user_name as user_name,rl.remark1 as pic,reward_money,contract_user,ua.contract_call,concat(province,\'-\',city,\'-\',area,\'-\',address) as address,\'\' as email, create_time as time from my_reward_list rl inner join my_user_address ua on rl.address_id = ua.user_address_id where rl.wish_id=:wid and  rl.pay_status=2';
        }
        $paramAry=[];
        switch($flag)
        {
            case 'up':
                $sql .= ' and reward_id < :rid';
                $paramAry[':rid'] = $start_id;
                break;
            case 'down':
                $sql .= ' and reward_id > :rid';
                $paramAry[':rid'] = $start_id;
                break;
            default:
                break;
        }
        $sql .= ' order by reward_id asc';
        $paramAry[':wid']=$wish->wish_id;
        $addressList = \Yii::$app->db->createCommand($sql,$paramAry)->queryAll();
        return $addressList;
    }

    /**
     * 获取随机打赏回复
     * @return mixed
     */
    public static function GetRandRewardRemark()
    {
        $configFile = __DIR__.'/../config/RewardRemardConfig.php';
        $wordsAry = require($configFile);
        $index = rand(1,count($wordsAry));
        return $wordsAry[$index -1];
    }

    /**
     * 退款
     * @param $rewardList
     */
    public static function BackMoneyByRewardList($wish,$rewardList, &$error)
    {
        //更新余额  业务日志  消息  打赏记录  愿望信息
        if(!isset($rewardList))
        {
            $error = '打赏记录不能为空';
            return false;
        }
        $transActions = [];
        $rewardList->is_back = 2;
        $transActions[] = new RewardListSaveForReward($rewardList,[]);

        $user_id = $rewardList->reward_user_id;
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户不存在';
            return false;
        }
        $billInfo = PersonalUserUtil::GetUserBillInfoByUserId($user_id);
        if(!isset($billInfo))
        {
            $error = '账户信息不存在';
            return false;
        }
        //$wish = WishUtil::GetWishRecordById($rewardList->wish_id);
        if(!isset($wish))
        {
            $error = '愿望信息不存在';
            return false;
        }
        if($rewardList->pay_status === 2)
        {
            $back_money = $rewardList->reward_money_except_packets;
            $transActions[] = new BalanceSaveForReward($billInfo,['modify_type'=>'add_balance','add_money'=>$back_money]);

            $transActions[]= new WishSaveByTrans($wish,['modify_type'=>'change_back_count_and_money','back_money'=>$back_money]);

            $businessLog = BusinessLogUtil::GetBusinessLogNew('265',$user);
            $businessLog->remark5 = strval($rewardList->reward_id);
            $businessLog->remark6 = strval($wish->wish_id);
            $businessLog->remark7 = strval($billInfo->account_info_id);
            $businessLog->remark9 = sprintf('系统对愿望【%s】进行了退款，退款金额【%s】，【%s】退款前的账户余额【%s】',$wish->wish_name,$back_money,$user->nick_name,$billInfo->balance);

            $transActions[] = new BusinessLogSaveForReward($businessLog,['error'=>'余额打赏愿望业务日志存储异常',
                'propertys'=>[
                    'remark10'=>[
                        'model'=>'user_bill',
                        'attr'=>'attributes',
                        'key_method'=>'SetRemark10ByUserAccountInfo',
                    ],
                ]]);

            $msgContent = sprintf('系统对未实现愿望进行了退款，退款金额【%s】，已经进入您的余额',$back_money);
            $msg = MessageUtil::GetMsgNewModel(71,$msgContent,$user_id);
            $transActions[] = new MessageSaveForReward($msg,[]);
        }


        if(!RewardUtil::RewardSaveByTransaction($transActions,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 获取退款的打赏记录
     * @param $wish_id
     */
    public static function GetBackRewardList($wish_id, $limit=10)
    {
        if(empty($limit))
        {
            $limit = 10;
        }
        $condition = ['and','is_back=1',['wish_id'=>$wish_id]];
        return RewardList::find()->limit($limit)->where($condition)->all();
    }

    /**
     * 查找是否支持过该愿望
     * @param $wish_id
     * @param $user_id
     */
    public static function GetOneRewardByUserId($wish_id,$user_id)
    {
        return RewardList::findOne([
            'wish_id'=>$wish_id,
            'reward_user_id'=>$user_id,
            'pay_status'=>'2',
        ]);
    }

    /**
     * 第三方支付支持取消
     * @param $rewardInfo
     * @param $error
     */
    public static function CancelRewardByOtherPay($rewardInfo,&$error)
    {
        $error = '';
        $transActions = [];
        $red_pakect_id = $rewardInfo->red_packets_id;
        if(!empty($red_pakect_id))
        {
            $red_pakect = RedPacketsUtil::GetPersonalRedPacketsById($red_pakect_id);
            if(!isset($red_pakect))
            {
                $error = '找不到红包记录';
                \Yii::getLogger()->log($error.' 个人红包red_packet_id'.$red_pakect_id, Logger::LEVEL_ERROR);
                return false;
            }
            if($red_pakect->status === 2)
            {
                $red_pakect->status = '0';//未使用
            }
            else
            {
                $error = '红包状态错误';
                \Yii::getLogger()->log($error.' 红包状态：'.strval($red_pakect->status).' 个人红包red_packet_id'. $red_pakect_id, Logger::LEVEL_ERROR);
                return false;
            }
            $transActions[] = new PersonalRedPacketsSaveForReward($red_pakect,[]);
        }
        if($rewardInfo->pay_status !== 1)
        {
            $error = '打赏记录状态错误，取消失败';
            return false;
        }

        $rewardInfo->pay_status = 0;//未支付
        $rewardInfo->red_packets_id = null;
        $rewardInfo->red_packets_money = '0.00';
        $transActions[] = new RewardListSaveForReward($rewardInfo,[]);
        if(!self::RewardSaveByTransaction($transActions, $error))
        {
            return false;
        }
        //\Yii::getLogger()->log('取消打赏支付，打赏id：'.$rewardInfo->reward_id,Logger::LEVEL_ERROR);
        return true;
    }

    /**
     * 根据支付账单号来获取打赏记录
     * @param $billNo
     */
    public static function GetRewardInfoByBillNo($billNo)
    {
        return RewardList::findOne(['pay_bill'=>$billNo]);
    }
    /**
     * 获取支持记录
     * @param $reward_id
     * @return null|static
     */
    public static function GetRewardInfoById($reward_id)
    {
        return RewardList::findOne(['reward_id'=>$reward_id]);
    }
    /**
     * 获取打赏记录
     * @param $wish
     * @param $user
     * @param $red_packet
     */
    public static function GetRewardListNewModel($wish,$user,$red_packet)
    {
        $model = new RewardList();
        $model->wish_id = $wish->wish_id;
        $model->reward_user_id = $user->account_id;
        $model->reward_user_name = $user->nick_name;
        $model->create_time = date('Y-m-d H:i:s');
        $model->pay_bill = WaterNumUtil::GenWaterNum('ZHF-',true,true,'2015-12-30',4);
        $model->pay_num = 1;
        $model->is_back = 1;
        $model->remark1 = $user->pic;
        $model->first_red_packet_money = '0';
        $model->red_packets_money = '0';
        $model->remark3 = ($user->centification_level > 0? '1':'0');
        if(isset($red_packet))
        {
            $model->red_packets_id = $red_packet->personal_packets_id;
            $model->red_packets_money = $red_packet->packets_money;
        }
        return $model;
    }
    /**
     * 阿里支付打赏信息保存
     * @param $passParam
     * @param $out
     * @param $error
     */
    public static function SaveAlipayReward($passParam,&$out,&$error)
    {
        $params = $passParam;
        if(!isset($params['wish_id']))
        {
            $error = '愿望id不能为空';
            return false;
        }
        if(!isset($params['reward_money']))
        {
            $error = '愿望id不能为空';
            return false;
        }
        if(!isset($params['real_pay_money']))
        {
            $error = '愿望id不能为空';
            return false;
        }
        $user_id = $passParam['user_id'];
        if(empty($user_id))
        {
            $error = '用户不能为空';
            return false;
        }
        unset( $passParam['user_id']);
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户信息找不到';
            return false;
        }
        $out = [];
        $wish_id = $params['wish_id'];
        $reward_money = $params['reward_money'];
        $real_pay_money = $params['real_pay_money'];
        $remark2 = $params['remark2'];
        //unset($passParam['params']);
        $wish = WishUtil::GetWishRecordById($wish_id);
        if(!isset($wish))
        {
            $error = '愿望不能为空';
            return false;
        }
       if(!WishUtil::CheckWishCouldReward($wish,'3',$user,$error))
       {
           return false;
       }
        if(doubleval($real_pay_money) <= 0.0)
        {
            $error = '支付金额必须大于0';
            return false;
        }
        if(doubleval($reward_money) <= 0)
        {
            $error = '打赏金额必须大于0';
            return false;
        }
        $packet_id = $passParam['red_packets_id'];
        $red_packet_money = 0.00;
        if(!empty($packet_id))
        {
            //处理红包
            $red_packet = RedPacketsUtil::GetPersonalRedPacketsById($packet_id);
            if(!isset($red_packet))
            {
                $error = '获取不到红包信息';
                return false;
            }
            if(!RedPacketsUtil::CheckPersonalRedPackets($red_packet,$user,$error,$reward_money,$wish))
            {
                return false;
            }
            $red_packet_money = $red_packet->packets_money;
        }
        //判断金额总金额问题
        if(doubleval($reward_money) !== (doubleval($real_pay_money) + doubleval($red_packet_money)))
        {
            $error = '金额错误';
            return false;
        }
        $rewardInfo = RewardUtil::GetRewardListNewModel($wish,$user,$red_packet);
        $rewardInfo->pay_status = 1;
        $rewardInfo->pay_type = 3;
        $rewardInfo->reward_money_except_packets = $real_pay_money;
        $rewardInfo->remark2 = $remark2;
        if(empty($rewardInfo->remark2))
        {
            $rewardInfo->remark2 = self::GetRandRewardRemark();
        }
        unset($params['real_pay_money']);
        unset($params['phone_no']);
        $rewardInfo->attributes = $params;
        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            if(!$rewardInfo->save())
            {
                \Yii::getLogger()->log(var_export($rewardInfo->getErrors(),true),Logger::LEVEL_ERROR);
               throw new Exception('打赏信息存储失败');
            }
            if(isset($red_packet))
            {
                $red_packet->status = 2;//使用中，取消支付可以撤销红包为未使用
                if(!$red_packet->save())
                {
                    throw new Exception('更新红包信息异常');
                }
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $trans->rollBack();
            return false;
        }

        $out = [
            'real_pay_money'=>strval($real_pay_money),
            'reward_record_id'=>strval($rewardInfo->reward_id),
             'bill_no'=>$rewardInfo->pay_bill
        ];
        return true;
    }


    /**
     * 微信支付打赏信息保存
     * @param $passParam
     * @param $out
     * @param $error
     */
    public static function SaveWxpayReward($passParam,&$out,&$error)
    {
        $params = $passParam;
        if(!isset($params['wish_id']))
        {
            $error = '愿望id不能为空';
            return false;
        }
        if(!isset($params['reward_money']))
        {
            $error = '打赏金额不能为空';
            return false;
        }
        if(!isset($params['real_pay_money']))
        {
            $error = '实际支付金额不能为空';
            return false;
        }
        $user_id = $passParam['user_id'];
        if(empty($user_id))
        {
            $error = '用户不能为空';
            return false;
        }
        unset( $passParam['user_id']);
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户信息找不到';
            return false;
        }
        $out = [];
        $wish_id = $params['wish_id'];
        $reward_money = $params['reward_money'];
        $real_pay_money = $params['real_pay_money'];
        $remark2 = $params['remark2'];
        //unset($passParam['params']);
        $wish = WishUtil::GetWishRecordById($wish_id);
        if(!isset($wish))
        {
            $error = '愿望不能为空';
            return false;
        }
        if(!WishUtil::CheckWishCouldReward($wish,'4',$user,$error))
        {
            return false;
        }
        if(doubleval($real_pay_money) <= 0.0)
        {
            $error = '支付金额必须大于0';
            return false;
        }
        if(doubleval($reward_money) <= 0)
        {
            $error = '打赏金额必须大于0';
            return false;
        }
        $red_packet_money = 0.00;
        $packet_id = $passParam['red_packets_id'];
        if(!empty($packet_id))
        {
            //处理红包
            $red_packet = RedPacketsUtil::GetPersonalRedPacketsById($packet_id);
            if(!isset($red_packet))
            {
                $error = '获取不到红包信息';
                return false;
            }
            if(!RedPacketsUtil::CheckPersonalRedPackets($red_packet,$user,$error,$reward_money,$wish))
            {
                return false;
            }
            $red_packet_money = $red_packet->packets_money;
        }
        //判断金额总金额问题
        if(doubleval($reward_money) !== (doubleval($real_pay_money) + doubleval($red_packet_money)))
        {
            $error = '金额错误';
            return false;
        }
        $rewardInfo = RewardUtil::GetRewardListNewModel($wish,$user,$red_packet);
        $rewardInfo->pay_status = 1;
        $rewardInfo->pay_type = 4;
        $rewardInfo->reward_money_except_packets = $real_pay_money;
        $rewardInfo->remark2 = $remark2;
        if(empty($rewardInfo->remark2))
        {
            $rewardInfo->remark2 = self::GetRandRewardRemark();
        }
        unset($params['real_pay_money']);
        unset($params['phone_no']);
        $rewardInfo->attributes = $params;
        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            if(!$rewardInfo->save())
            {
                \Yii::getLogger()->log(var_export($rewardInfo->getErrors(),true),Logger::LEVEL_ERROR);
                throw new Exception('打赏信息存储失败');
            }
            if(isset($red_packet))
            {
                $red_packet->status = 2;//使用中，取消支付可以撤销红包为未使用
                if(!$red_packet->save())
                {
                    throw new Exception('更新红包信息异常');
                }
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $trans->rollBack();
            return false;
        }
        $out = [
            'bill_no'=>$rewardInfo->pay_bill,
            'reward_id'=>$rewardInfo->reward_id,
            'real_pay_money'=>$real_pay_money
        ];
        return true;
    }

    /**
     * 微信支付打赏信息保存
     * @param $passParam
     * @param $out
     * @param $error
     */
    public static function SaveLlpayReward($passParam,&$out,&$error)
    {
        $params = $passParam;
        if(!isset($params['wish_id']))
        {
            $error = '愿望id不能为空';
            return false;
        }
        if(!isset($params['reward_money']))
        {
            $error = '打赏金额不能为空';
            return false;
        }
        if(!isset($params['real_pay_money']))
        {
            $error = '实际支付金额不能为空';
            return false;
        }
        $user_id = $passParam['user_id'];
        if(empty($user_id))
        {
            $error = '用户不能为空';
            return false;
        }
        unset( $passParam['user_id']);
        $user = PersonalUserUtil::GetAccontInfoById($user_id);
        if(!isset($user))
        {
            $error = '用户信息找不到';
            return false;
        }
        $out = [];
        $wish_id = $params['wish_id'];
        $reward_money = $params['reward_money'];
        $real_pay_money = $params['real_pay_money'];
        $remark2 = $params['remark2'];
        //unset($passParam['params']);
        $wish = WishUtil::GetWishRecordById($wish_id);
        if(!isset($wish))
        {
            $error = '愿望不能为空';
            return false;
        }
        if(!WishUtil::CheckWishCouldReward($wish,'5',$user,$error))
        {
            return false;
        }
        if(doubleval($real_pay_money) <= 0.0)
        {
            $error = '支付金额必须大于0';
            return false;
        }
        if(doubleval($reward_money) <= 0)
        {
            $error = '打赏金额必须大于0';
            return false;
        }
        $red_packet_money = 0.00;
        $packet_id = $passParam['red_packets_id'];
        if(!empty($packet_id))
        {
            //处理红包
            $red_packet = RedPacketsUtil::GetPersonalRedPacketsById($packet_id);
            if(!isset($red_packet))
            {
                $error = '获取不到红包信息';
                return false;
            }
            if(!RedPacketsUtil::CheckPersonalRedPackets($red_packet,$user,$error,$reward_money,$wish))
            {
                return false;
            }
            $red_packet_money = $red_packet->packets_money;
        }
        //判断金额总金额问题
        if(doubleval($reward_money) !== (doubleval($real_pay_money) + doubleval($red_packet_money)))
        {
            $error = '金额错误';
            return false;
        }
        $rewardInfo = RewardUtil::GetRewardListNewModel($wish,$user,$red_packet);
        $rewardInfo->pay_status = 1;
        $rewardInfo->pay_type = 4;
        $rewardInfo->reward_money_except_packets = $real_pay_money;
        $rewardInfo->remark2 = $remark2;
        if(empty($rewardInfo->remark2))
        {
            $rewardInfo->remark2 = self::GetRandRewardRemark();
        }
        unset($params['real_pay_money']);
        unset($params['phone_no']);
        $rewardInfo->attributes = $params;
        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            if(!$rewardInfo->save())
            {
                \Yii::getLogger()->log(var_export($rewardInfo->getErrors(),true),Logger::LEVEL_ERROR);
                throw new Exception('打赏信息存储失败');
            }
            if(isset($red_packet))
            {
                $red_packet->status = 2;//使用中，取消支付可以撤销红包为未使用
                if(!$red_packet->save())
                {
                    throw new Exception('更新红包信息异常');
                }
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $trans->rollBack();
            return false;
        }
        $out = [
            'bill_no'=>$rewardInfo->pay_bill,
            'reward_id'=>$rewardInfo->reward_id,
            'real_pay_money'=>$real_pay_money,
            'user_id'=>$user_id,
            'phone_no'=>$user->phone_no,
        ];
        return true;
    }

    /**
     * 余额打赏
     * @param $attrs
     * @param $wishRecord
     * @param $user
     * @param $billInfo
     * @param $pay_money
     * @param $redPackets,
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SaveBalanceRewardInfo($attrs,$wishRecord,$user,$billInfo,$pay_money,$redPackets,&$error)
    {
        $error = '';
        if(!PersonalUserUtil::CouldRewardByBalance($user->device_no,$error))
        {
            return false;
        }
        $transActions = [];
        $packetsMoney = 0.0;
        if(isset($redPackets))
        {
            $packetsMoney = doubleval($redPackets->packets_money);
            $redPackets->status = 1;
            $transActions[] = new PersonalRedPacketsSaveForReward($redPackets,[]);
        }

        $pay_left_money = doubleval($attrs['reward_money_except_packets']);

        $transActions[] = new BalanceSaveForReward($billInfo,['modify_type'=>'sub_balance','sub_money'=>$pay_left_money]);

        //余额操作日志
        $transActions[] = new CreateUserBalanceLogByTrans($billInfo,['op_money'=>$pay_left_money,'operate_type'=>'2']);

        $transActions[] = new WishSaveForReward($wishRecord,['pay_left_money'=>$pay_left_money,'packetsMoney'=>$packetsMoney]);



        $rewardModel = new RewardList();
        $rewardModel->attributes = $attrs;
        $rewardModel->is_back = 1;
        $rewardModel->first_red_packet_money = '0';
        if(empty($rewardModel->remark2))
        {
            $rewardModel->remark2 =self::GetRandRewardRemark();
        }
        $transActions[] = new RewardListSaveForReward($rewardModel,[]);

        $userActive = UserActiveUtil::GetUserActiveByUserId($user->account_id);
        if(!isset($userActive))
        {
            $error = '活跃度信息丢失';
            return false;
        }

        $transActions[] = new UserActiveSaveForReward($userActive,['reward_money'=>$pay_money]);


        //创建日志
        $rewardLog = new BusinessLog();
        $rewardLog->operate_type = 1;
        $rewardLog->remark1 = $pay_money;
        $rewardLog->remark3 = strval($user->account_id);
        $rewardLog->remark4 = $user->nick_name;
        $rewardLog->remark5 = strval($wishRecord->wish_id);
        $rewardLog->remark6 = $wishRecord->wish_name;
        $rewardLog->remark7 = strval($billInfo->account_info_id);
        $rewardLog->remark9 = sprintf('%s打赏了愿望【%s】，打赏金额【%s】,打赏除红包外金额【%s】，打赏红包金额【%s】，愿望金额【%s】，打赏前除红包金额【%s】，打赏前红包金额【%s】',
            $user->nick_name,
            $wishRecord->wish_name,
            $pay_money,
            $pay_left_money,
            $packetsMoney,
            $wishRecord->wish_money,
            $wishRecord->ready_reward_money,
            $wishRecord->red_packets_money);
        $rewardLog->remark13 = date('Y-m-d H:i:s',time());
        $transActions[] = new BusinessLogSaveForReward($rewardLog,['error'=>'余额打赏愿望业务日志存储异常',
            'propertys'=>[
                'remark10'=>[
                    'model'=>'user_bill',
                    'attr'=>'attributes',
                    'key_method'=>'SetRemark10ByUserAccountInfo',
                ],
            ]]);


        //创建消息
        $msgContent = sprintf('您打赏了愿望【%s】',$wishRecord->wish_name);
        $sendMsg = sprintf('[%s]打赏了愿望【%s】，金额【%s】',$user->nick_name,$wishRecord->wish_name,$pay_money);
        $msg =MessageUtil::GetMsgNewModel(2,$msgContent,$user->account_id);
        $transActions[] = new MessageSaveForReward($msg,[]);

        $friendsList = [];
        //判断是否不是愿望发起人的好友，不是的话加好友
        $wishPublishId = $wishRecord->publish_user_id;
        $user_id = $user->account_id;
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
        if(!RewardUtil::RewardSaveByTransaction($transActions,$error))
        {
            \Yii::getLogger()->log('账户余额打赏提交失败'.$error,Logger::LEVEL_ERROR);
            return false;
        }
        //环信加为好友
        foreach($friendsList as $friend)
        {
            if(!ChatUtilHuanXin::AddUserFriends($friend[0],$friend[1],$error))
            {
                \Yii::getLogger()->log('环信加为好友失败：'.$error,Logger::LEVEL_ERROR);
            }
            if(!ChatUtilHuanXin::AddUserFriends($friend[1],$friend[0],$error))
            {
                \Yii::getLogger()->log('环信加为好友失败：'.$error,Logger::LEVEL_ERROR);
            }
        }
        //加入愿望群
        if(!self::AddUserToGroup($wishRecord->wish_id,$user_id,$error1))
        {
            \Yii::getLogger()->log('加入愿望群异常：'.$error1,Logger::LEVEL_ERROR);
        }
        else
        {
            $other_group_id = $error1;
            //发送群消息
            if(!ChatUtilHuanXin::ChatSendMsg([strval($other_group_id)],$sendMsg,$error,'chatgroups'))
            {
                \Yii::getLogger()->log('打赏发布愿望发送环信消息失败：'.$error,Logger::LEVEL_ERROR);
            }
        }
        //发送环信打赏消息
        if(!ChatUtilHuanXin::ChatSendMsg([strval($wishPublishId)],$sendMsg,$error))
        {
            \Yii::getLogger()->log('打赏发布愿望发送环信消息失败：'.$error,Logger::LEVEL_ERROR);
        }
        return true;
    }

    /**
     * 加入愿望群
     * @param $wish_id
     * @param $user_id
     */
    public static function AddUserToGroup($wish_id,$user_id,&$error)
    {
        $group = ChatGroupUtil::GetGroupByWishId($wish_id);
        if(isset($group))
        {
            if(!ChatUtil::AddUserToGroup($group->chat_group_id,$user_id,$error))
            {
                return false;
            }
            else
            {
                $error = $group->other_id;//返回环信群id
            }
        }
        else
        {
            $error = '愿望不存在群,wish_id:'.$wish_id;
        }
        return true;
    }

    /**
     * 美愿基金打赏
     * @param $attrs
     * @param $attrBorrowRecord
     * @param $checkAttrs
     * @param $userFundInfo
     * @param $wishRecord
     * @param $user
     * @param $pay_money
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SaveFundRewardInfo($attrs,$attrBorrowRecord,$checkAttrs,$userFundInfo,$wishRecord,$user,$pay_money,$redPackets,&$error)
    {
        $error = '';
        $transActions = [];
        $pay_left_money = doubleval($attrs['reward_money_except_packets']);
        $packetsMoney = 0.0;
        if(isset($redPackets))
        {
            $packetsMoney = doubleval($redPackets->packets_money);
            $redPackets->status = 1;
            $transActions[] = new PersonalRedPacketsSaveForReward($redPackets,[]);
        }

        //审核通过后，加入金额,将支付状态改为已支付
        //$transActions[] = new WishSaveForReward($wishRecord,['pay_left_money'=>$pay_left_money,'packetsMoney'=>$packetsMoney]);

        //如果审核失败，这里值要加回去
        $userFundInfo->credit_balance = doubleval($userFundInfo->credit_balance) - $pay_left_money;
        $userFundInfo->borrow_money_sum = doubleval($userFundInfo->borrow_money_sum) + $pay_left_money;
        $transActions[] = new FundSaveForReward($userFundInfo,[]);

        $rewardModel = new RewardList();
        $rewardModel->attributes = $attrs;
        $rewardModel->is_back = 1;
        $rewardModel->first_red_packet_money = '0';
        if(empty($rewardModel->remark2))
        {
            $rewardModel->remark2 = self::GetRandRewardRemark();
        }
        if(isset($redPackets))
        {
            $rewardModel->red_packets_id = $redPackets->personal_packets_id;
        }
        $rewardModel->red_packets_money = $packetsMoney;
        $transActions[] = new RewardListSaveForReward($rewardModel,[]);

        //借款记录
        $borrowFundModel = new BorrowFund();
        $borrowFundModel->attributes = $attrBorrowRecord;
        $transActions[] = new BorrowFundSaveForReward($borrowFundModel,[
            'propertys'=>
            [ 'reward_id'=>
                [
                    'model'=>'reward_list',
                   'attr'=>'reward_id'
                ],
            ],
        ]);

        //审核记录
        $checkBusinessModel = new BusinessCheck();
        $checkBusinessModel->attributes = $checkAttrs;
        $transActions[] = new CheckRecordSaveForReward($checkBusinessModel,[
            'propertys'=>[
                'relate_id'=>[
                    'model'=>'borrow_fund',
                    'attr'=>'borrow_fund_id',
                ],
            ],
        ]);

        //创建日志
        $rewardLog = new BusinessLog();
        $rewardLog->operate_type = 1;
        $rewardLog->remark1 = $pay_left_money;
        $rewardLog->remark3 = strval($user->account_id);
        $rewardLog->remark4 = $user->nick_name;
        $rewardLog->remark5 = strval($wishRecord->wish_id);
        $rewardLog->remark6 = $wishRecord->wish_name;
        //$rewardLog->remark7 = $billInfo->account_info_id;
        $rewardLog->remark9 = sprintf('您用美愿基金打赏了愿望【%s】，借款审核中',$wishRecord->wish_name);
        $rewardLog->remark13 = date('Y-m-d H:i:s',time());
        $transActions[] = new BusinessLogSaveForReward($rewardLog,['error'=>'美愿基金打赏日志存储失败']);

        //创建消息
        $msgContent = sprintf('您用美愿基金打赏了愿望【%s】，借款审核中',$user->nick_name,$wishRecord->wish_name);
        $msg =MessageUtil::GetMsgNewModel(2,$msgContent,$user->account_id);
        $transActions[] = new MessageSaveForReward($msg,[]);



        if(!RewardUtil::RewardSaveByTransaction($transActions, $error))
        {
            return false;
        }
        return true;
    }

    /**
     * 事物保存支持
     * @param $objList  //需要保存的对象数组，
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function RewardSaveByTransaction($objList,&$outInfo,&$error)
    {
        $error ='';
        if(!isset($objList) || !is_array($objList))
        {
            $error = '非法对象，不是数组';
            return false;
        }
        foreach($objList as $obj)
        {
            if(!($obj instanceof ISaveForTransaction))
            {
                $error = '对象数组中存在非ISaveForTransaction对象';
                return false;
            }
        }
        $outInfo = [];
        $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);
        try
        {
            foreach($objList as $obj)
            {
                if(!$obj->SaveRecordForTransaction($error,$outInfo))
                {
                    if(is_array($error))
                    {
                        \Yii::getLogger()->log(var_export($error,true).' type:'.var_export($obj,true),Logger::LEVEL_ERROR);
                    }
                    else
                    {
                        \Yii::getLogger()->log($error.' type:'.var_export($obj,true),Logger::LEVEL_ERROR);
                    }
                    $trans->rollBack();
                    return false;
                }
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

    /**
     * 获取支付宝支付未知支付状态记录
     */
    public static function GetAlipayUnkownPayResultRecords($limit=100)
    {
        $query = RewardList::find();
        $query->select(['reward_id','pay_status','pay_bill'])
            ->limit($limit)
            ->where(['pay_status'=>1,'pay_type'=>3]);
        return $query->all();
    }

    /**
     * 根据唯一号获取信息
     * @param $op_unique_no
     * @return null|static
     */
    public static function GetRewardinfoByLivingUniqueOn($op_unique_no){
        return Reward::findOne(['op_unique_no'=>$op_unique_no]);
    }

    /***
     * 打赏记录票数保存数据
     * @param $user_id
     * @param $living_master_id
     * @param $gift_value
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SaveSumRewardTickets($user_id,$living_master_id,$gift_value,&$error)
    {
        $sql = 'insert ignore into mb_sum_reward_tickets (reward_user_id,living_master_id,ticket_num,create_time)
values(:ruid,:luid,:tnum,:ctime)';
        $updatesql = 'update mb_sum_reward_tickets set ticket_num = ticket_num+:tnum,create_time=:ctime where reward_user_id=:ruid and living_master_id=:luid';

        $result = \Yii::$app->db->createCommand($sql,[
            ':ruid' => $user_id,
            ':luid' => $living_master_id,
            ':tnum' => 0,
            ':ctime' => date('Y-m-d H:i:s',time())
        ])->execute();

        $up_result = \Yii::$app->db->createCommand($updatesql,[
            ':ruid' => $user_id,
            ':luid' => $living_master_id,
            ':tnum' => $gift_value,
            ':ctime' => date('Y-m-d H:i:s',time())
        ])->execute();

        if($up_result <= 0){
            $error = '打赏累计记录更新失败';
            return false;
        }
        return true;
    }

    /**
     * 汇总周打赏票数，只统计实际票数
     * @param $reward_user_id
     * @param $gift_value
     * @param $week
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SaveSumWeekSendTickets($reward_user_id,$gift_value,$week,&$error)
    {
        $sql = 'insert ignore into mb_sum_week_reward_tickets (reward_user_id,date_week,ticket_num)
values(:ruid,:wk,:tnum)';
        $updatesql = 'update mb_sum_week_reward_tickets set ticket_num = ticket_num+:tnum where reward_user_id=:ruid and date_week=:wk';

        $result = \Yii::$app->db->createCommand($sql,[
            ':ruid' => $reward_user_id,
            ':tnum' => 0,
            ':wk' => $week
        ])->execute();

        $up_result = \Yii::$app->db->createCommand($updatesql,[
            ':ruid' => $reward_user_id,
            ':tnum' => $gift_value,
            ':wk' => $week
        ])->execute();

        if($up_result <= 0){
            $error = '打赏周累计记录更新失败';
            return false;
        }
        return true;
    }

    /**
     * TODO: 同步处理用户财务和主播财务信息(送礼物)
     * @param $userBalance //TODO: 送礼物用户的财务信息
     * @param $hostBalance //TODO: 主播的财务信息
     * @param $params //TODO: 额外参数
     * @param $giftData //TODO: 礼物参数
     * @param $out //TODO: 返回数据
     * @param $error //TODO: 错误信息
     * @return bool
     *
     */
    public static function giftTicketsHandle($userBalance,$hostBalance,$params,$giftData,&$out,&$error)
    {
        $moneyType = $params['moneyType'];
        //TODO: 增加礼物打赏日志
        $reward_params = [
            'gift_value' => $params['giftValue'],
            'gift_name' => $params['giftName'],
            'money_type' => $params['moneyType'],
            'op_unique_no' => $params['op_unique_no'],
            'gift_id' => $params['giftId'],
            'user_id' => $params['userId'],
            'multiple' => $params['multiple'],
            'total_gift_value' => $params['giftValue'],
            'receive_rate' => $params['receive_rate'],
        ];
        //TODO: 处理用户打赏记录信息 返回记录ID
        $transAction = new LivingMasterRewardByTrans($params['living_before_id'],$params['living_master_id'],$reward_params);
        if(!$transAction->SaveRecordForTransaction($error,$out)) {
            return false;
        }
       
        $relate_id = $out['relate_id']; //TODO: 获取打赏记录 Id
        $userBalanceLog = [
            'unique_id' => $params['op_unique_no'],
            'op_value' => $giftData->gift_value,
            'relate_id' => $relate_id,
            'money_type' => $moneyType,
            'device_type'=> $params['deviceType'],
        ];
        $hostBalanceLog = $userBalanceLog;
        $hostBalanceLog['op_value'] = $params['giftValue'];
        //TODO: 实际 or 虚拟
        if($moneyType == 1)
        {
            //TODO: 送礼物用户实际豆处理
            $transActions[] = new TicketMyMoneyTrans($userBalance,['gift_value'=>$giftData->gift_value,'user_id'=>$params['userId']]);
            $userBalanceLog['field'] = 'bean_balance';
            $userBalanceLog['operate_type'] = 6;
            //TODO: 主播逻辑收到金额处理
            $transActions[] = new TicketLivingMasterMoneyTrans($hostBalance,['gift_value'=>$params['giftValue'],'living_master_id'=>$params['living_master_id']]);
            $hostBalanceLog['field'] = 'ticket_count';
            $hostBalanceLog['operate_type'] = 7;
            $transActions[] = new CreateUserBalanceLogByTrans($hostBalance,$hostBalanceLog);
            $hostBalanceLog['field'] = 'ticket_real_sum';
            $transActions[] = new CreateUserBalanceLogByTrans($hostBalance,$hostBalanceLog);
        }
        else if($moneyType == 2)
        {
            //TODO: 送礼物用户虚拟豆处理
            $transActions[] = new VirtualTicketMyMoneyTrans($userBalance,['gift_value'=>$giftData->gift_value,'user_id'=>$params['userId']]);
            $userBalanceLog['field'] = 'virtual_bean_balance';
            $userBalanceLog['operate_type'] = 8;
            //TODO: 主播逻辑收到虚拟金额处理
            $hostBalanceLog['field'] = 'virtual_ticket_count';
            $hostBalanceLog['operate_type'] = 9;
            $transActions[] = new VirtualTicketLivingMasterMoneyTrans($hostBalance,['gift_value'=>$params['giftValue'],'living_master_id'=>$params['living_master_id']]);
            $transActions[] = new CreateUserBalanceLogByTrans($hostBalance,$hostBalanceLog);
        }

        $transActions[] = new CreateUserBalanceLogByTrans($userBalance,$userBalanceLog);
        $userBalanceLog['field'] = 'send_ticket_count';
        $userBalanceLog['operate_type'] = 10;
        $transActions[] = new CreateUserBalanceLogByTrans($userBalance,$userBalanceLog);
        $hostBalanceLog['field'] = 'ticket_count_sum';
        $transActions[] = new CreateUserBalanceLogByTrans($hostBalance,$hostBalanceLog);

        if (!SaveByTransUtil::RewardSaveByTransaction($transActions, $error, $outInfo)) {
            return false;
        }
        $out['relate_id'] = $relate_id;
        return true;
    }

}