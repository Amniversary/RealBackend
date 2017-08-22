<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 下午5:18
 */

namespace backend\controllers\SignActions;


use backend\business\WeChatUserUtil;
use common\models\AttentionEvent;
use common\models\SignMessage;
use yii\base\Action;

class CreateMsgAction extends Action
{
    public function run()
    {
        $id = \Yii::$app->request->get('id');
        $Cache = WeChatUserUtil::getCacheInfo();
        $model = new AttentionEvent();
        $model->app_id =  $Cache['record_id'];
        $model->flag = 3;
        $model->msg_type = 0;
        $model->create_time = date('Y-m-d H:i:s');
        $model->order_no = 50;
        $model->global = 0;
        $load = \Yii::$app->request->post();
        if(!empty($load)){
            if($load['AttentionEvent']['msg_type'] == 2){
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if($model->load($load) && $model->save()){
            $message = new SignMessage();
            $message->sign_id = $id;
            $message->msg_id = $model->record_id;
            if($message->save()){
                return $this->controller->redirect(['index_msg', 'id'=>$id]);
            }
        }else{
            return $this->controller->render('createmsg',[
                'model'=>$model,
                'cache'=>$Cache,
                'id'=>$id
            ]);
        }
    }
}