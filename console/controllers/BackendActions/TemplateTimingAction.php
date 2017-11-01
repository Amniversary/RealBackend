<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/16
 * Time: 下午6:12
 */

namespace console\controllers\BackendActions;


use backend\business\JobUtil;
use common\models\TemplateTiming;
use yii\base\Action;

class TemplateTimingAction extends Action
{
    public function run()
    {
        set_time_limit(0);
        $Data = $this->getTemplateTask();
        if (empty($Data)) {
            return false;
        }
        foreach ($Data as $list) {
            $task = TemplateTiming::findOne(['id' => $list->id]);
            $task->status = 0;
            $task->save();
        }
        $count = count($Data);
        foreach ($Data as $item) {
            $params = [
                'key_word' => 'template_task',
                'id' => $item->template_id,
                'data' => json_decode($item->template_data, true),
                'app_id' => $item->app_id,
                'task_id' => $item->id,
                'type' => $item->type
            ];
            if (!JobUtil::AddCustomJob('templateBeanstalk', 'task', $params, $error, (60 * 60 * 24 * 30))) {
                \Yii::error('job error :' . var_export($error, true));
                var_dump($error);
                exit;
            }
        }
        $date = date('Y-m-d H:i:s');
        echo "执行 $count 条模板任务, 时间 : $date \n";
        exit;
    }


    private function getTemplateTask()
    {
        $time = time();
        $condition = 'create_time <= :tm and type in (1,2) and status = 1';
        $query = TemplateTiming::find()
            ->select(['id', 'app_id', 'template_id', 'template_data', 'status', 'type', 'create_time'])
            ->where($condition, [':tm' => $time])
            ->all();

        return $query;
    }
}