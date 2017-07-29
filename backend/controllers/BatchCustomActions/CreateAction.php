<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/28
 * Time: 下午2:10
 */

namespace backend\controllers\BatchCustomActions;


use common\models\SystemMenu;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $model = new SystemMenu();
        $model->status = 1;
        if($model->load(\Yii::$app->request->post()))
        {
            if(!$model->save()){
                $error = '保存配置记录失败:';
                \Yii::error($error. ' '. var_export($model->getErrors(),true));
            }
            return $this->controller->redirect('index');
        } else {
            return $this->controller->render('_form',[
                'model'=>$model
            ]);
        }
    }
}