<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 下午5:18
 */

namespace backend\controllers\LaterActions;


use backend\business\WeChatUserUtil;
use common\models\AttentionEvent;
use common\models\LaterImage;
use common\models\SignMessage;
use yii\base\Action;

class CreateMsgAction extends Action
{
    public function run()
    {
        $id = \Yii::$app->request->get('id');
        $model = new LaterImage();
        $model->later_id = $id;
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->controller->redirect(['index_msg', 'id' => $id]);
        } else {
            return $this->controller->render('create_msg', [
                'model' => $model,
                'id' => $id
            ]);
        }
    }
}