<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/7
 * Time: 下午3:32
 */

namespace backend\controllers\TemplateActions;


use common\models\AttentionEvent;
use common\models\BatchCustomerParams;
use yii\base\Action;

class CreateParamsMsgAction extends Action
{
    public function run()
    {
        $id = \Yii::$app->request->get('id');
        $model = new AttentionEvent();
        $model->flag = 4;
        $model->msg_type = 0;
        $model->create_time = date('Y-m-d H:i:s');
        $model->order_no = 50;
        $model->global = 0;
        $load = \Yii::$app->request->post();
        if(!empty($load)){
            if($load['AttentionEvent']['msg_type'] == 2) {
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if($model->load($load) && $model->save()){
            $message = new BatchCustomerParams();
            $message->task_id = $id;
            $message->msg_id = $model->record_id;
            if($message->save()){
                return $this->controller->redirect(['index_msg', 'id'=>$id]);
            }
        }else{
            return $this->controller->render('create_params_msg',[
                'model'=>$model,
                'id'=>$id
            ]);
        }
    }
}