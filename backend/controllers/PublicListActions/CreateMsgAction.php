<?php
namespace backend\controllers\PublicListActions;


use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use common\models\AttentionEvent;
use yii\base\Action;

class CreateMsgAction extends Action
{
    public function run()
    {
        $model = new AttentionEvent();
        $Cache = WeChatUserUtil::getCacheInfo();
        $model->app_id = $Cache['record_id'];
        $model->flag = 0;
        $model->msg_type = 0;
        $model->order_no = 50;
        $model->global = 0;
        $model->create_time = date('Y-m-d H:i:s');
        $load = \Yii::$app->request->post();
        if(!empty($load)){
            if($load['AttentionEvent']['msg_type'] == 2){
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if($model->load($load) && $model->save()){
            return $this->controller->redirect('attention');
        }else{
            return $this->controller->render('createmsg',[
                'model'=>$model,
                'cache'=>$Cache
            ]);
        }
    }
}