<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/7
 * Time: 下午5:18
 */

namespace backend\controllers\TemplateActions;


use backend\components\ExitUtil;
use common\models\AttentionEvent;
use common\models\Resource;
use yii\base\Action;

class UpdateMsgAction extends Action
{
    public function run($record_id)
    {
        $model = AttentionEvent::findOne(['record_id' => $record_id]);
        if (empty($record_id)) {
            ExitUtil::ExitWithMessage('消息记录不存在');
        }
        $id = \Yii::$app->request->get('id');
        $load = \Yii::$app->request->post();
        if (!empty($load)) {
            if ($load['AttentionEvent']['msg_type'] == 2) {
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if ($model->load($load) && $model->save()) {
            Resource::deleteAll(['msg_id' => $record_id]);
            return $this->controller->redirect(['index_msg', 'id'=>$id]);
        } else {
            return $this->controller->render('create_params_msg', [
                'model' => $model,
                'id'=> $id
            ]);
        }
    }
}