<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/15
 * Time: 上午10:05
 */

namespace backend\controllers\BatchCustomActions;


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
        $menu_id = \Yii::$app->request->get('menu_id');
        $load = \Yii::$app->request->post();
        if (!empty($load)) {
            if ($load['AttentionEvent']['msg_type'] == 2) {
                $load['AttentionEvent']['picurl'] = $load['AttentionEvent']['picurl1'];
            }
        }
        if ($model->load($load) && $model->save()) {
            Resource::deleteAll(['msg_id'=>$record_id]);
            return $this->controller->redirect(['index_msg','menu_id'=>$menu_id,'id'=>$id]);
        } else {
            return $this->controller->render('createmsg', [
                'model' => $model,
                'menu_id'=>$menu_id,
                'id'=>$id
            ]);
        }
    }

}