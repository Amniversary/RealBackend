<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v3;

use common\components\DeviceUtil;
use common\components\tenxunlivingsdk\TimRestApi;
use frontend\business\ApiCommon;
use frontend\business\BalanceUtil;
use frontend\business\ClientActiveUtil;
use frontend\business\ClientUtil;
use frontend\business\GiftUtil;
use frontend\business\GoldsAccountUtil;
use frontend\business\IntegralAccountUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use frontend\business\LuckyGiftUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\LivingMasterRewardByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\TicketMyMoneyTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\VirtualTicketMyMoneyTrans;
use frontend\zhiboapi\IApiExcute;
use Pili\Stream;
use yii\log\Logger;

/**
 * Class 测试协议
 * @package frontend\zhiboapi\v2
 */
class TestSendGift implements IApiExcute
{

    private function check_param_ok($dataProtocal, &$error = '')
    {
        $fields = ['unique_no','gift_id','living_id','op_unique_no','money_type'];
        $fieldLabels = ['唯一号','礼物 ID','直播间id','操作码','操作类型'];
        $len = count($fields);
        for($i = 0; $i < $len; $i++)
        {
            if(!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }

        if(!in_array($dataProtocal['data']['money_type'],[1,2]))
        {
            $error = '参数错误';
            return false;
        }
        return true;
    }
    /**
     * 测试送礼物
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }
        $giftId = $dataProtocal['data']['gift_id'];
        $op_unique_no = $dataProtocal['data']['op_unique_no'];
        $money_type = $dataProtocal['data']['money_type'];
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($uniqueNo, $LoginInfo, $error))
        {
            return false;
        }

        $gift_object = GiftUtil::GetGiftById($giftId);
        if (!isset($gift_object->gift_id))
        {
            $error = '礼物不存在';
            return false;
        }

        //获取用户账户信息
        $banlances_object = BalanceUtil::GetUserBalanceByUserId($LoginInfo['user_id']);
        if(!isset($banlances_object))
        {
            \Yii::getLogger()->log('LoginInfo===:'.var_export($LoginInfo,true),Logger::LEVEL_ERROR);
            $error = '用户账户信息不存在';
            return false;
        }
        //判断用户是否被冻结
        if($banlances_object['freeze_status']==2)
        {
            //状态为2表示已冻结
            $error = '账号冻结请联系客服';
            return false;
        }
        //实际
        if($money_type == 1)
        {
            if ($banlances_object->bean_balance < $gift_object->gift_value)
            {
                $error = '鲜花余额不足';
                return false;
            }
        }
        else
        {
            //虚拟
            if ($banlances_object->virtual_bean_balance < $gift_object->gift_value)
            {
                $error = '活动余额不足';
                return false;
            }
        }

        $living_info = LivingUtil::GetSendGiftLivingInfo($dataProtocal['data']['living_id']);
        if(!$living_info)
        {
            $error = '直播不存在';
            return false;
        }

        if(($dataProtocal['data']['money_type'] == 1) && ($gift_object->lucky_gift == 1))
        {
            $client_active = ClientActiveUtil::GetClientActiveInfoByUserId($LoginInfo['user_id']);
            /****幸运礼物判断***/
//            if(!LuckyGiftUtil::GetLuckGiftRate($living_info['living_master_id'],$living_info['other_id'],$gift_object->gift_value,$living_info['level_no'],$LoginInfo['nick_name'],$outInfo,$error))
            $params = [
                'user_id' => $LoginInfo['user_id'],
                'living_master_id' => $living_info['living_master_id'],
                'other_id' => $living_info['other_id'],
                'gift_value' => $gift_object->gift_value,
                'level_no' => $client_active->level_no,
                'nick_name' => $LoginInfo['nick_name'],
                'relate_id' => '',
                'device_type' => $dataProtocal['device_type']
            ];
            if(!LuckyGiftUtil::GetLuckGiftRate($banlances_object,$params,$outInfo,$error))
            {
                \Yii::getLogger()->log('幸运礼物   Error==:'.$error,Logger::LEVEL_ERROR);
            }

            $gift_value = $outInfo['total_gift_value'];
        }

        if(!isset($gift_value) || empty($gift_value))
        {
            $gift_value = $gift_object->gift_value;
            $outInfo['multiple'] = 1;
            $outInfo['total_gift_value'] = $gift_object->gift_value;
            $outInfo['receive_rate'] = 1;
        }
        /***打赏***/
        $reward_params = [
            'gift_value' => $gift_value,
            'gift_name' => $gift_object->gift_name,
            'money_type' => $dataProtocal['data']['money_type'],
            'op_unique_no' => $dataProtocal['data']['op_unique_no'],
            'gift_id' => $gift_object->gift_id,
            'user_id' => $LoginInfo['user_id'],
            'multiple' => $outInfo['multiple'],
            'total_gift_value' => $gift_value,
            'receive_rate' => $outInfo['receive_rate'],
        ];
        $transActions[] = new LivingMasterRewardByTrans($living_info['living_before_id'],$living_info['living_master_id'],$reward_params);

        /***实际豆处理***/
        $money_params = [
            'gift_value' => $gift_object->gift_value,
            'user_id' => $LoginInfo['user_id'],
            'living_master_id' => $living_info['living_master_id'],
        ];

        $extend_params = [
            'unique_id' => $dataProtocal['data']['op_unique_no'],
            'op_value' => $gift_object->gift_value,
            'relate_id' => '',
            'money_type' => $dataProtocal['data']['money_type'],
        ];

        if($dataProtocal['data']['money_type'] == 1)
        {
            /**当前用户豆处理**/
            $extend_params['device_type'] = $dataProtocal['device_type'];
            $transActions[] = new TicketMyMoneyTrans($banlances_object,$money_params);
            $extend_params['field'] = 'bean_balance';
            $extend_params['operate_type'] = 6;
            $transActions[] = new CreateUserBalanceLogByTrans($banlances_object,$extend_params);
            $extend_params['operate_type'] = 10;
            $extend_params['field'] = 'send_ticket_count';
            $transActions[] = new CreateUserBalanceLogByTrans($banlances_object,$extend_params);


        }

        //虚拟豆处理
        if($dataProtocal['data']['money_type'] == 2)
        {
            /**当前用户虚拟豆处理**/
            $extend_params['device_type'] = $dataProtocal['device_type'];
            $transActions[] = new VirtualTicketMyMoneyTrans($banlances_object,$money_params);
            $extend_params['field'] = 'virtual_bean_balance';
            $extend_params['operate_type'] = 8;
            $transActions[] = new CreateUserBalanceLogByTrans($banlances_object,$extend_params);
            $extend_params['field'] = 'send_ticket_count';
            $extend_params['operate_type'] = 10;
            $transActions[] = new CreateUserBalanceLogByTrans($banlances_object,$extend_params);
        }

        if (!RewardUtil::RewardSaveByTransaction($transActions, $outInfo, $error))
        {
            return false;
        }
        $relate_id = $outInfo['relate_id'];

        $tickets_num = $living_info['ticket_count_sum']+$gift_value;

        /***主播豆数处理***/
        $banlances_data = [
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
        }

        if($gift_object->world_gift == 2 )      //世界礼物
        {
            $world_gift_data = [
                'user_id' => $LoginInfo['user_id'],
                'gift_name' => $gift_object->gift_name,
                'send_nick_name' => $LoginInfo['nick_name'],
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
}