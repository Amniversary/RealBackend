<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7
 * Time: 14:41
 */

namespace console\controllers\BackendActions;

use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ActivityGirlDaySaveByTrans;
use yii\base\Action;
use yii\db\Query;

class AddActivityGirlAction extends Action
{
    // 更新数据库间隔
    const UPDATE_INTERVAL = 10;

    const SYS_ID = 0;

    public function run()
    {
        $now = time();
        $base = $this->getStarTime();
        if (($now - intval($base['value'])) > self::UPDATE_INTERVAL ) {
            // 先修改计时器，防止并发
            if ($base) {
                $getValue = (new Query())
                    ->select(['living_master_id', 'sum(gift_value) as gift_value'])
                    ->from('mb_reward')
                    ->where('gift_type = 1 and status = 1 and create_time between :stat and :end',[
                        ':stat'=>date('Y-m-d H:i:s', $base['value']),
                        ':end'=>date('Y-m-d H:i:s', $now)])
                    ->groupBy('living_master_id')
                    ->orderBy(['gift_value'=>SORT_DESC])->all();

                $rewardValue =(new Query())
                    ->select(['reward_user_id', 'sum(gift_value) as gift_value'])
                    ->from('mb_reward')
                    ->where('gift_type = 1 and status = 1 and create_time between :stat and :end',[
                        ':stat'=>date('Y-m-d H:i:s', $base['value']),
                        ':end'=>date('Y-m-d H:i:s', $now)])
                    ->groupBy('reward_user_id')
                    ->orderBy(['gift_value'=>SORT_DESC])->all();

                $transActions[] = new ActivityGirlDaySaveByTrans($getValue,$rewardValue);

                if(!SaveByTransUtil::RewardSaveByTransaction($transActions,$error))
                {
                    echo " $error\n";
                    exit;
                }

                $effectRow = $this->upStarTime($now,$base);
                if($effectRow <= 0)
                {
                    echo " 更新时间节点失败\n";
                    exit;
                }
                $time = date('Y-m-d H:i:s');
                echo " update ok data: $time \n";
            }
            else
            {
                echo " Update statistics time error\n";
                exit;
            }
        }
        else{
            echo " 活动时间未开始\n";
        }
    }

    /**
     * 获取统计数据开始时间
     */
    private function getStarTime()
    {
        $query = (new Query())
            ->from('mb_activity_girl')
            ->where(['user_id' => self::SYS_ID])
            ->one();
        return $query;
    }

    /**
     * 更新统计时间
     */
    private function upStarTime($now,$base)
    {
        $sql = 'update mb_activity_girl set value=:ct WHERE user_id=:id AND value=:ot';
        $effectRow = \Yii::$app->db->createCommand($sql, [
            ':ct' => $now,
            ':id' => $base['user_id'],
            ':ot' => $base['value'],
        ])->execute();

        return $effectRow;
    }
} 