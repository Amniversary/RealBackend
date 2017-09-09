<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/7
 * Time: 下午3:11
 */

namespace backend\controllers\TemplateActions;


use common\models\AttentionEvent;
use common\models\BatchCustomer;
use common\models\BatchCustomerParams;
use yii\base\Action;
use yii\base\Exception;

class DeleteParamsAction extends Action
{
    public function run($id)
    {
        $rst = ['code'=> 1, 'msg'=>''];
        if(empty($id) || !isset($id)) {
            $rst['msg'] = '任务id 不能为空';
            echo json_encode($rst);exit;
        }

        $task = BatchCustomer::findOne(['id'=>$id]);
        if(empty($task) || !isset($task)) {
            $rst['msg'] = '任务记录不存在或已删除';
            echo json_encode($rst);exit;
        }

        try{
            $list = BatchCustomerParams::findAll(['task_id'=> $id]);
            $trans = \Yii::$app->db->beginTransaction();
            if(!$task->delete()){
                $rst['msg'] = '删除失败';
                \Yii::error($rst['msg']. '  :'. var_export($task->getError(),true));
                echo json_encode($rst);exit;
            }
            foreach($list as $item) {
                (new AttentionEvent())->deleteAll(['record_id'=> $item['msg_id']]);
            }
            (new BatchCustomerParams())->deleteAll(['task_id'=> $id]);
            $trans->commit();
        }catch(Exception $e) {
            $trans->rollBack();
            $rst['msg'] = $e->getMessage();
            echo json_encode($rst);exit;
        }

        $rst['code'] = 0;
        echo json_encode($rst);
    }
}