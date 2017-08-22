<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/29
 * Time: 下午4:20
 */

namespace backend\controllers\SignActions;


use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use common\models\Keywords;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $model = new Keywords();
        $model->global = 3;
        if($model->load(\Yii::$app->request->post()) && $model->save()){
            return $this->controller->redirect('keyword');
        }else{
            return $this->controller->render('_form',[
                'model'=>$model,
            ]);
        }
    }
}