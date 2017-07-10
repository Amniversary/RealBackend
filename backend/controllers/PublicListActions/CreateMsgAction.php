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

        if($model->load(\Yii::$app->request->post()) && $model->save()){
            if($model->msg_type == 2){
                $rst = (new WeChatUtil())->UploadWeChatImg($model->picurl);
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