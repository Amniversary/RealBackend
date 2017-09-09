<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/8
 * Time: 上午11:27
 */

namespace backend\controllers\TemplateActions;


use backend\business\CustomUtil;
use backend\business\JobUtil;
use yii\base\Action;

class StartTaskAction extends Action
{
    public function run($id)
    {
        $rst = ['code' => 1, 'msg' => ''];
        if (empty($id)) {
            $rst['msg'] = '任务id, 不能为空';
            echo json_encode($rst);
            exit;
        }
        $task = CustomUtil::getCustomerById($id);
        if (empty($task) || !isset($task)) {
            $rst['msg'] = '任务记录不存在或已删除';
            echo json_encode($rst);
            exit;
        }
        switch (intval($task->status)) {
            case 0:$msg = '任务已结束';break;
            case 2:$msg = '任务已开始';break;
            default:$msg = '';break;
        }
        if ($task->status != 1) {
            $rst['msg'] = $msg;
            echo json_encode($rst);
            exit;
        }
        $decode = json_decode($task->app_list);
        if (empty($decode)) {
            $rst['msg'] = '发送的公众号列表数据为空';
            echo json_encode($rst);
            exit;
        }
        $msgData = CustomUtil::getCustomerMsg($task->id);
        if(empty($msgData)) {
            $rst['msg'] = '消息列表为空';
            echo json_encode($rst);exit;
        }
        $task->status = 2;
        if (empty($task->create_time)) {
            $params = [
                'key_word' => 'batch_customer',
                'task_id' => $task->id,
                'msgData'=> $msgData
            ];
            foreach ($decode as $item) {
                $params['app_id'] = $item;
                if (!JobUtil::AddCustomJob('templateBeanstalk', 'batch_customer', $params, $error, (60 * 60 * 24))) {
                    \Yii::error('job error :' . $error);
                }
            }
            $task->status = 0;
        }
        if (!$task->save()) {
            $rst['msg'] = '修改任务状态失败 -- 2';
            \Yii::error($rst['msg'] . ' : ' . var_export($task->getErrors(), true));
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = 0;
        echo json_encode($rst);
    }
}