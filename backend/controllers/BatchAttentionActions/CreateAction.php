<?php
namespace backend\controllers\BatchAttentionActions;



use common\models\AttentionEvent;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $model = new AttentionEvent();
        $model->app_id = 0;
        $model->flag = 0;
        $model->msg_type = 0;
        $model->order_no = 50;
        $model->global = 1;
        $model->create_time = date('Y-m-d H:i:s');
        $load = \Yii::$app->request->post();
        if(!empty($load)){
            if($load['AttentionEvent']['msg_type'] == 2){
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if($model->load($load) && $model->save()){
            return $this->controller->redirect('index');
        }else{
            return $this->controller->render('_form',[
                'model'=>$model,
            ]);
        }
    }
}