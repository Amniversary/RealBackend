<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 下午2:18
 */

namespace backend\controllers\SignActions;


use backend\business\WeChatUserUtil;
use common\models\SignParams;
use yii\base\Action;

class CreateParamsAction extends Action
{
    public function run()
    {
        $cache = WeChatUserUtil::getCacheInfo();
        $model = new SignParams();
        $model->app_id = $cache['record_id'];
        $model->day_id = 1;
        $model->type = 0;
        if($model->load(\Yii::$app->request->post()) && $model->save()) {
            return $this->controller->redirect('index');
        } else{
            return $this->controller->render('_formparams',[
                'model' => $model,
                'cache' => $cache
            ]);
        }
    }
}