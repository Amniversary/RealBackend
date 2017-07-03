<?php
namespace backend\controllers\PublicListActions;


use backend\business\WeChatUserUtil;
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
        $model->create_time = date('Y-m-d H:i:s');

        if($model->load(\Yii::$app->request->post()) && $model->save()){
            return $this->controller->redirect('attention');
        }else{
            return $this->controller->render('createmsg',[
                'model'=>$model,
                'cache'=>$Cache
            ]);
        }
    }
}