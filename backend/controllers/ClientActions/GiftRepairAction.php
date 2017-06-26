<?php
/**
 * 修复送出礼物未收到
 */

namespace backend\controllers\ClientActions;

use yii\base\Action;
use frontend\business\BalanceUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\TicketLivingMasterMoneyTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\VirtualTicketLivingMasterMoneyTrans;

class GiftRepairAction extends Action
{
    public function run()
    {

        $sql = 'SELECT mb_reward.reward_user_id, mb_reward.sum_gift_value, mb_experience_log.sum_experience from (SELECT reward_user_id, sum( mb_reward.gift_value) as sum_gift_value from mb_reward where reward_id >= 5181988 group by reward_user_id) as mb_reward
LEFT JOIN (select sum(experience) as sum_experience, user_id from mb_experience_log where source_type = 1 and log_id >= 55147296 GROUP BY user_id) as mb_experience_log on mb_reward.reward_user_id = mb_experience_log.user_id';
        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        foreach ($result as $row) {
            $userId = $row['reward_user_id'];
            $giftValue = $row['sum_gift_value'];
            $experience = $row['sum_experience'];


            $experience_num = $giftValue * 10 - $experience; //当前经验值
            //创建经验日志参数
            $extend_params = [
                'device_type' => 1,
                'user_id' => $userId,
                'source_type' => 1, //送礼物
                'living_before_id' => 1,
                'change_rate' => 10,
                'experience' => $experience_num,
                'create_time' => date('Y-m-d H:i:s'),
                'gift_value' => $experience_num / 10,
                'relate_id' => ' ',
            ];

            $transActions = [];
            /***经验小于等于0不处理***/
            if ($experience_num > 0)
            {
                $clentActive = \frontend\business\ClientActiveUtil::GetClientActiveInfoByUserId($userId);
                $transActions[] = new \frontend\business\SaveRecordByransactions\SaveByTransaction\ExperienceModifyByTrans($clentActive, ['experience_num' => $experience_num]);
                $transActions[] = new \frontend\business\SaveRecordByransactions\SaveByTransaction\CreateExperienceLogByTrans($clentActive, $extend_params);
                if (!\frontend\business\RewardUtil::RewardSaveByTransaction($transActions, $outInfo, $error)) {
                    var_dump($error);
                }

                echo $userId;
                echo '<br>';
                echo $experience_num;
                echo '<br>';
                echo '<br>';
            }
        }
        echo 'do';
        exit;
        echo '<pre>';
        $sql = "select mb_reward.reward_id, mb_reward.reward_user_id, mb_reward.living_master_id,
                mb_reward.gift_name, mb_reward.gift_value, mb_reward.create_time, mb_reward.op_unique_no,  count(mb_client_balance_log.relate_id)
                from mb_reward
                left join mb_client_balance_log on mb_reward.reward_id = mb_client_balance_log.relate_id and mb_client_balance_log.operate_type in (7,9)
                where mb_reward.create_time BETWEEN '2017-03-20 00:00:00' and '2017-03-24 00:00:00'
                group by mb_reward.reward_id
                having count(mb_client_balance_log.relate_id) = 0
                order by mb_reward.reward_id desc
                limit 1000";

        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        foreach ($result as $row) {
            $tran   = [];
            $params = [
                'operate_type' => 7,
                'unique_id'   => $row['op_unique_no'],
                'op_value'    => $row['gift_value'],
                'relate_id'   => $row['reward_id'],
                'money_type'  => 1,
                'device_type' => 3
            ];

            $balance = BalanceUtil::GetUserBalanceByUserId($row['living_master_id']);

            // 修改余额表
            $tran[] = new TicketLivingMasterMoneyTrans($balance, [
                'gift_value'       => $row['gift_value'],
                'living_master_id' => $row['living_master_id']
            ]);

            // 修改日志表
            $params['field'] = 'ticket_real_sum';
            $tran[] = new CreateUserBalanceLogByTrans($balance, $params);

            $params['field'] = 'ticket_count_sum';
            $tran[] = new CreateUserBalanceLogByTrans($balance, $params);

            $params['field'] = 'ticket_count';
            $tran[] = new CreateUserBalanceLogByTrans($balance, $params);

            $outInfo = '';
            $error   = '';
            var_dump(RewardUtil::RewardSaveByTransaction($tran, $outInfo, $error));
            var_dump($error);
        }
    }
} 