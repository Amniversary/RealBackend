<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/8
 * Time: 上午9:55
 */

namespace backend\controllers\TemplateActions;


use backend\business\TagUtil;
use common\models\BatchCustomer;
use yii\base\Action;

class SetAuthAction extends Action
{
    public function run($id)
    {
        $rst = ['code' => 1, 'msg' => ''];
        if (empty($id)) {
            $rst['msg'] = '任务id, 不能为空';
            echo json_encode($rst);
            exit;
        }
        $task = BatchCustomer::findOne(['id' => $id]);
        if (empty($task) || !isset($task)) {
            $rst['msg'] = '任务记录不存在';
            echo json_encode($rst);
            exit;
        }
        $params = \Yii::$app->request->post('title');
        if (isset($params)) {
            $error = '';
            if(!TagUtil::SaveTemplateParams($params, $task, $error)) {
                $rst['msg'] = $error;
                echo json_encode($rst);exit;
            }
            $rst['code'] = 0;
            echo json_encode($rst);
            exit;
        } else {
            $task->app_list = '';
            $task->save();
            $rst['code'] = 0;
            echo json_encode($rst);
            exit;
        }
    }
}