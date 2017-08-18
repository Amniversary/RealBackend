<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午3:50
 */

namespace backend\controllers\BatchKeyWordActions;


use common\models\AttentionEvent;
use yii\base\Action;

class CreateMsgAction extends Action
{
    public function run()
    {
        $key_id = \Yii::$app->request->get('key_id');
        $model = new AttentionEvent();
        $model->flag = 1;
        $model->msg_type = 0;
        $model->create_time = date('Y-m-d H:i:s');
        $model->order_no = 50;
        $model->global = 1;
        $load = \Yii::$app->request->post();
        if(!empty($load)){
            if($load['AttentionEvent']['msg_type'] == 2){
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if($model->load($load)){
            if(!$model->save()){
                \Yii::error('保存批量关键词消息失败:'.var_export($model->getErrors(),true));
            }
            return $this->controller->redirect(['indexson','key_id'=>$key_id]);
        }else{
            return $this->controller->render('createmsg',[
                'model'=>$model,
                'key_id'=>$key_id
            ]);
        }
    }
}