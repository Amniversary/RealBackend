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
        $model->create_time = date('Y-m-d H:i:s');
        $load = \Yii::$app->request->post();
        if(!empty($load)){
            if($load['AttentionEvent']['msg_type'] == 2){
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if($model->load($load) && $model->save()){
            if($model->msg_type == 2){
                $rst = (new WeChatUtil())->UploadWeChatImg($model->picurl,$Cache['authorizer_access_token']);
                $model->media_id = $rst['media_id'];
                $model->update_time = $rst['created_at'];
                $model->save();
            }
            return $this->controller->redirect('attention');
        }else{
            return $this->controller->render('createmsg',[
                'model'=>$model,
                'cache'=>$Cache
            ]);
        }
    }
}