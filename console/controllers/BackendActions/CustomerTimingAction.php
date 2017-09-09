<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/9
 * Time: 上午11:15
 */

namespace console\controllers\BackendActions;


use backend\business\CustomUtil;
use backend\business\JobUtil;
use common\models\BatchCustomer;
use yii\base\Action;
use yii\db\Query;

class CustomerTimingAction extends Action
{
    public function run()
    {
        set_time_limit(0);
        $taskData = $this->getTaskData();
        if (empty($taskData)) return false;
        foreach ($taskData as $item) {
            $task = BatchCustomer::findOne(['id' => $item['id']]);
            $task->status = 0;
            $task->save();
        }
        $count = count($taskData);
        foreach ($taskData as $list) {
            $decode = json_decode($list['app_list']);
            $msgData = CustomUtil::getCustomerMsg($list['id']);
            $params = [
                'key_word' => 'batch_customer',
                'task_id' => $list['task_id'],
                'msgData' => $msgData,
            ];
            foreach ($decode as $v) {
                $params['app_id'] = $v;
                if (!JobUtil::AddCustomJob('templateBeanstalk', 'batch_customer', $params, $error, (60 * 60 * 48))) {
                    \Yii::error('job error :'. $error);
                }
            }
        }
        $date = date('Y-m-d H:i:s');
        echo "执行群发客服消息任务 $count 条,  时间 : $date \n";
        exit;
    }

    private function getTaskData()
    {
        $time = time();
        $condition = 'create_time <= :time and status = 2';
        $query = (new Query())
            ->select(['id', 'status', 'create_time', 'app_list'])
            ->from('wc_batch_customer')
            ->where($condition, [':time' => $time])
            ->all();

        return $query;
    }
}