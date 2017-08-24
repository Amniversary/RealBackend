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
use common\models\SignImage;
use yii\base\Action;

class BatchUpdateMsgAction extends Action
{
    public function run($record_id)
    {
        $id = \Yii::$app->request->get('id');
        $model = SignImage::findOne(['id' => $record_id]);
        if (empty($model)) {
            ExitUtil::ExitWithMessage('消息记录不存在');
        }
        $load = \Yii::$app->request->post();
        if ($model->load($load) && $model->save()) {
            return $this->controller->redirect(['batch_index_msg', 'id'=>$id]);
        } else {
            return $this->controller->render('batchcreatemsg', [
                'model' => $model,
                'id'=>$id
            ]);
        }
    }
}