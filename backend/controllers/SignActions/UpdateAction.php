<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午3:29
 */

namespace backend\controllers\SignActions;


use backend\business\WeChatUserUtil;
use backend\components\ExitUtil;
use common\models\Keywords;
use yii\base\Action;

class UpdateAction extends Action
{
    public function run($key_id){
        $model = Keywords::findOne(['key_id'=>$key_id]);
        if(empty($key_id)){
            ExitUtil::ExitWithMessage('关键词记录不存在');
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->controller->redirect(['keyword']);
        } else {
            return $this->controller->render('_form', [
                'model' => $model,
            ]);
        }
    }
}