<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 下午5:18
 */

namespace backend\controllers\SignActions;


use backend\business\WeChatUserUtil;
use common\models\AttentionEvent;
use common\models\SignImage;
use common\models\SignMessage;
use yii\base\Action;

class BatchCreateMsgAction extends Action
{
    public function run()
    {
        $id = \Yii::$app->request->get('id');
        $model = new SignImage();
        $model->sign_id = $id;
        $load = \Yii::$app->request->post();
        if($model->load($load) && $model->save()){
            return $this->controller->redirect(['batch_index_msg', 'id'=>$id]);
        }else{
            return $this->controller->render('batchcreatemsg',[
                'model'=>$model,
                'id'=>$id
            ]);
        }
    }
}