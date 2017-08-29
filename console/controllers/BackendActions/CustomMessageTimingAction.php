<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/24
 * Time: 下午4:42
 */

namespace console\controllers\BackendActions;


use backend\business\JobUtil;
use common\models\TemplateTiming;
use yii\base\Action;

class CustomMessageTimingAction extends Action
{
    public function run()
    {
        set_time_limit(0);
        $msgData = $this->getCustomTask();
        if(empty($msgData)) {
            return false;
        }
        foreach($msgData as  $item) {
            $task = TemplateTiming::findOne(['id'=>$item['id']]);
            $task->status = 0;
            $task->save();
        }
        $count = count($msgData);
        foreach($msgData as $list) {
            $params = [
                'key_word'=>'send_batch_msg',
                'data'=>json_decode($list['template_data']),
                'app_id'=>$list['app_id'],
                'type'=>$list['type'],
            ];
            if(!JobUtil::AddCustomJob('templateBeanstalk', 'send_user_msg', $params, $error, (60*60*5))) {
                \Yii::error($error);
                var_dump($error);
                return false;
            }
        }
        $date = date('Y-m-d H:i:s');
        echo "执行 $count 条模板任务, 时间 : $date \n";
        exit;
    }

    private function getCustomTask()
    {
        $time = time();
        $condition = 'create_time <= :tm and type in (3,4) and status = 1';
        $query = TemplateTiming::find()
            ->select(['id','app_id', 'template_id', 'template_data', 'status', 'type' , 'create_time'])
            ->where($condition,[':tm'=>$time])
            ->all();

        return $query;
    }
}