<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 上午9:50
 */

namespace backend\controllers\SignActions;


use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use backend\components\ExitUtil;
use common\models\AttentionEvent;
use common\models\Resource;
use yii\base\Action;

class UpdateMsgAction extends Action
{
    public function run($record_id)
    {
        $id = \Yii::$app->request->get('id');
        $model = AttentionEvent::findOne(['record_id' => $record_id]);
        if (empty($record_id)) {
            ExitUtil::ExitWithMessage('消息记录不存在');
        }
        $Cache = WeChatUserUtil::getCacheInfo();
        $load = \Yii::$app->request->post();
        if (!empty($load)) {
            if ($load['AttentionEvent']['msg_type'] == 2) {
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if ($model->load($load) && $model->save()) {
            Resource::deleteAll(['msg_id'=>$record_id]);
            return $this->controller->redirect(['index_msg', 'id'=>$id]);
        } else {
            return $this->controller->render('createmsg', [
                'model' => $model,
                'cache' => $Cache,
                'id'=>$id
            ]);
        }
    }
}