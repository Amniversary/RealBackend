<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3;

use common\components\tenxunlivingsdk\TimRestApi;
use common\models\Balance;
use frontend\business\ApiCommon;
use frontend\business\BalanceUtil;
use frontend\business\ClientActiveUtil;
use frontend\business\ClientUtil;
use frontend\business\GiftUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use frontend\business\LuckyGiftUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\LivingMasterRewardByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\TicketLivingMasterMoneyTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\TicketMyMoneyTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\VirtualTicketLivingMasterMoneyTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\VirtualTicketMyMoneyTrans;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * Class 送礼物
 * @package frontend\zhiboapi\v3
 */
class ZhiBoSendGift implements IApiExcute
{
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        if(!$this->check_params_ok($dataProtocal,$error))
        {
            return false;
        }
        $uniqueNo = $dataProtocal['data']['unique_no'];
        $moneyType = $dataProtocal['data']['money_type'];
        $giftId = $dataProtocal['data']['gift_id'];
        $livingId = $dataProtocal['data']['living_id'];
        $deviceType = $dataProtocal['device_type'];
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }
        $userId = $LoginInfo['user_id'];
        $userInfo = ClientUtil::getClientActive($userId);
        if(!in_array($moneyType,[1,2]))
        {
            $error = '参数错误';
            return false;
        }

        $gift_object = GiftUtil::GetGiftById($giftId);
        if (empty($gift_object->gift_id))
        {
            $error = '礼物不存在';
            return false;
        }

        //获取用户账户信息
        $banlances_object = BalanceUtil::GetUserBalanceByUserId($userId);
        if(!$this->check_balance($banlances_object,$moneyType,$gift_object,$userId,$error)){
            return false;
        }

        $living_info = LivingUtil::GetSendGiftLivingInfo($livingId);
        if(empty($living_info)) {
            $error = '直播不存在';
            \Yii::error($error.' : living_id:'.$livingId);
            return false;
        }
        $living_master_id = $living_info['living_master_id'];

        $hostBalance = BalanceUtil::GetUserBalanceByUserId($living_master_id);
        $gift_value = $gift_object->gift_value;
        $outInfo['multiple'] = 1;
        $outInfo['total_gift_value'] = $gift_object->gift_value;
        $outInfo['receive_rate'] = 1;
        if(($moneyType == 1) && ($gift_object->lucky_gift == 1))
        {
            $client_active = ClientActiveUtil::GetClientActiveInfoByUserId($LoginInfo['user_id']);
            /****幸运礼物判断***/
            $params = [
                'user_id' => $LoginInfo['user_id'],
                'living_master_id' => $living_info['living_master_id'],
                'other_id' => $living_info['other_id'],
                'gift_value' => $gift_object->gift_value,
                'level_no' => $client_active->level_no,
                'nick_name' => $userInfo['nick_name'],
                'relate_id' => '',
                'device_type' => $dataProtocal['device_type']
            ];
            if(!LuckyGiftUtil::GetLuckGiftRate($banlances_object,$params,$outInfo,$error))
            {
                \Yii::getLogger()->log('幸运礼物   Error==:'.$error,Logger::LEVEL_ERROR);
            }

            $gift_value = $outInfo['total_gift_value'];
        }

        /***打赏***/
        $reward_params = [
            'giftValue'=>$gift_value,
            'giftName'=>$gift_object->gift_name,
            'moneyType'=>$moneyType,
            'op_unique_no'=>$dataProtocal['data']['op_unique_no'],
            'giftId'=>$giftId,
            'userId'=>$userId,
            'multiple'=>$outInfo['multiple'],
            'total_gift_value'=>$gift_value,
            'receive_rate'=>$outInfo['receive_rate'],
            'living_before_id'=>$living_info['living_before_id'],
            'living_master_id'=>$living_master_id,
            'deviceType'=>$deviceType,
        ];
        //TODO: 事务处理用户扣豆和主播逻辑同步
        if (!RewardUtil::giftTicketsHandle($banlances_object,$hostBalance,$reward_params,$gift_object,$out,$error)) {
            return false;
        }
        $relate_id = $out['relate_id'];
        $tickets_num = $living_info['ticket_count_sum']+ (float)$gift_value;

        /***主播豆数处理***/
        /*$banlances_data = [
            'user_id' => $LoginInfo['user_id'],
            'tickets_num' => $tickets_num,
            'other_id' => $living_info['other_id'],
            'gift_value' => $gift_value,
//            'lucky_gift_value' => $outInfo['lucky_gift_value'],
            'lucky_gift_value' => $gift_value,
            'living_master_id' => $living_info['living_master_id'],
            'unique_id' => $dataProtocal['data']['op_unique_no'],
            'op_value' => $gift_value,
            'relate_id' => $relate_id,
            'money_type' => $dataProtocal['data']['money_type'],
            'balance_id' => $living_info['balance_id'],
            'ticket_count_sum' => $living_info['ticket_count_sum'],
            'device_type' => $dataProtocal['device_type'],

        ];
        if(!JobUtil::AddGetTicketJob('living_master_balance',$banlances_data,$error)){
            return false;
        }*/
        //TODO: 发送礼物im消息 (腾讯)
        $im_data = [
            'key_word'=>'send_gift_im',
            'user_id' => $userId,
            'tickets_num' => $tickets_num,
            'other_id' => $living_info['other_id']
        ];
        if(!JobUtil::AddImJob('tencent_im',$im_data,$error))
        {
            \Yii::error($error.' 礼物im发送失败');
        }

        if($gift_object->world_gift == 2 )      //世界礼物
        {
            $world_gift_data = [
                'user_id' => $LoginInfo['user_id'],
                'gift_name' => $gift_object->gift_name,
                'send_nick_name' => $userInfo['nick_name'],
                'accept_nick_name' => $living_info['nick_name'],
                'living_master_id' => $living_info['living_master_id'],
            ];
            \Yii::getLogger()->log('世界礼物数据'.var_export($world_gift_data,true),Logger::LEVEL_ERROR);
            if(!JobUtil::AddWorldGiftJob('world_gift',$world_gift_data,$error)){
                return false;
            }
        }

        /***将礼物积分信息处理***/
        $gift_score = [
            'living_master_id'=>$living_info['living_master_id'],
            'gift_id'=>$gift_object->gift_id,
            'send_user_id'=>$LoginInfo['user_id'],
        ];
        if(!JobUtil::AddGiftScoreJob('living_master_score',$gift_score,$error))
        {
            return false;
        }

        /***主播打赏记录处理队列***/
        $ticket_data = [
            'gift_value' => $gift_value,
            'user_id' => $LoginInfo['user_id'],
            'living_master_id' => $living_info['living_master_id'],
        ];
        if(!JobUtil::AddLivingTicketJob('living_master_ticket',$ticket_data,$error)){
            return false;
        }

        /***直播票数处理队列***/
        $living_ticket_data = [
            'user_id' => $LoginInfo['user_id'],
            'tickets_num' => $tickets_num,
            'other_id' => $living_info['other_id'],
            'living_id' => $living_info['living_id'],
            'gift_value' => $gift_value,
            'living_master_id' => $living_info['living_master_id'],
            'living_tickets_id' => $living_info['living_tickets_id'],
            'money_type' => $dataProtocal['data']['money_type'],
        ];
        if(!JobUtil::AddLivingTicketJob('living_ticket',$living_ticket_data,$error)){
            return false;
        }

        /***主播日，周，月票统计队列***/
        $ticket_statistic_data = [
            'gift_value' => $gift_value,
            'living_master_id' => $living_info['living_master_id'],
            'money_type' => $dataProtocal['data']['money_type'],
        ];
        if(!JobUtil::AddGetTicketJob('living_master_ticket_statistic',$ticket_statistic_data,$error)){
            return false;
        }
        /***打赏用户经验处理队列***/
        $exp_data = [
            'living_id' => $dataProtocal['data']['living_id'],
            'living_before_id' => $living_info['living_before_id'],
            'living_master_id' => $living_info['living_master_id'],
            'user_id' => $LoginInfo['user_id'],
            'money_type' => $dataProtocal['data']['money_type'],
            'gift_value' => $gift_object->gift_value,
            'relate_id' => $relate_id,
            'device_type' => $dataProtocal['data']['money_type'],
        ];
        if(!JobUtil::AddExpJob('send_living_experience',$exp_data,$error)){
            return false;
        }

        if($dataProtocal['data']['money_type'] == 1)
        {
            //打赏者汇总队列
            $sum_data = [
                'week' => date('Y-W'),
                'send_user_id' => $LoginInfo['user_id'],
                'gift_value' => $gift_value,
            ];
            if(!JobUtil::AddCustomJob('livingTicketBeanstalk','send_gift_sum',$sum_data,$error))
            {
                return false;
            }
        }


        $rstData['has_data'] = 1;
        $rstData['data_type'] = 'jsonarray';
        $rstData['data']['op_unique_no'] = $dataProtocal['data']['op_unique_no'];
        $rstData['data']['gift_id'] = $gift_object->gift_id;
        $rstData['data']['tickets_num'] = $tickets_num;

        return true;
    }

    /**
     * //TODO: 验证参数
     * @param $dataProtocal //TODO: 登录参数
     * @param $error
     * @return bool
     */
    private function check_params_ok($dataProtocal,&$error)
    {
        $fields = ['unique_no','gift_id','op_unique_no','money_type'];
        $fieldLabels = ['唯一号','礼物ID','操作码','金额类型'];
        $len = count($fields);
        for($i = 0; $i < $len; $i++) {
            if(!isset($dataProtocal['data'][$fields[$i]]) ||
                empty($dataProtocal['data'][$fields[$i]])){
                $error = $fieldLabels[$i].'，不能为空';
                return false;
            }
        }
        return true;
    }

    private function check_balance($balance,$moneyType,$giftData,$userId,&$error)
    {
        if (!isset($balance)) {
            $error = '用户账户信息不存在';
            \Yii::error($error.'1:  userId:'.$userId);
            return false;
        }
        //TODO:判断用户是否被冻结 状态为2表示已冻结
        if($balance['freeze_status'] == 2) {
            $error = '账号冻结请联系客服';
            return false;
        }

        //TODO: 判断实际 or 虚拟鲜花余额
        if($moneyType == 1) {
            if ($balance['bean_balance'] < $giftData['gift_value']) {
                $error = '鲜花余额不足';
                \Yii::error($error.'1: '.var_export($balance,true));
                return false;
            }
        }
        else if($moneyType == 2) {
            if ($balance['virtual_bean_balance'] < $giftData['gift_value']) {
                $error = '活动余额不足';
                return false;
            }
        }
        return true;
    }
}


