<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 13:55
 */
namespace backend\controllers\AdvertiseActions;


use common\models\Advertise;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = 'App启动首页管理';
        $model = new Advertise();
        if(  $model->load(\Yii::$app->request->post()) )
        {
            $model->status = 1;
            $model->effe_time = $model->effe_time.' 00:00:00';
            $model->end_time  = $model->end_time.' 23:59:59';
            $model->save();
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }


}