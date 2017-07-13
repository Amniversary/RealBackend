<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午3:50
 */

namespace backend\controllers\KeyWordActions;


use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use backend\components\ExitUtil;
use common\components\UsualFunForNetWorkHelper;
use common\models\AttentionEvent;
use common\models\Keywords;
use yii\base\Action;
use yii\web\HttpException;

class CreateMsgAction extends Action
{
    public function run()
    {
        $Cache = WeChatUserUtil::getCacheInfo();
        $model = new AttentionEvent();
        $model->app_id =  $Cache['record_id'];
        $model->flag = 1;
        $model->msg_type = 0;
        $model->create_time = date('Y-m-d H:i:s');

        if($model->load(\Yii::$app->request->post()) && $model->save()){
            if($model->msg_type == 2){
                $rst = (new WeChatUtil())->UploadWeChatImg($model->picurl,$Cache['authorizer_access_token']);
                $model->media_id = $rst['media_id'];
                $model->update_time = $rst['created_at'];
                $model->save();
            }
            return $this->controller->redirect('keyword');
        }else{
            return $this->controller->render('createmsg',[
                'model'=>$model,
                'cache'=>$Cache
            ]);
        }
    }
}