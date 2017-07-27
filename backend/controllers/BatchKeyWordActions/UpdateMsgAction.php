<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 上午9:50
 */

namespace backend\controllers\BatchKeyWordActions;


use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use backend\components\ExitUtil;
use common\models\AttentionEvent;
use yii\base\Action;

class UpdateMsgAction extends Action
{
    public function run($record_id)
    {
        $model = AttentionEvent::findOne(['record_id'=>$record_id]);
        if(empty($record_id)){
            ExitUtil::ExitWithMessage('消息记录不存在');
        }
        $load = \Yii::$app->request->post();
        if(!empty($load)){
            if($load['AttentionEvent']['msg_type'] == 2){
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if ($model->load($load) && $model->save()) {
            return $this->controller->redirect(['indexson','key_id'=>$model->key_id]);
        } else {
            return $this->controller->render('createmsg', [
                'model' => $model,
                'key_id'=>$model->key_id
            ]);
        }
    }
}