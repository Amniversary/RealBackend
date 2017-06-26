<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 13:54
 */
namespace backend\controllers\AdvertiseActions;

use yii;
use common\models\Advertise;
use yii\base\Action;
use backend\components\ExitUtil;

class UpdateAction extends Action
{
    public function run( $id )
    {
        $this->controller->enableCsrfValidation = false;
        $model = Advertise::findOne(['id'=>$id]);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('广告不存在');
        }
        if($model->load(\Yii::$app->request->post()))
        {
            $model->end_time = $model->end_time." 23:59:59";
            if($model->save()){
                return $this->controller->redirect(['index']);
            }
        }
        return $this->controller->render('update',
            [
                'model' => $model,
            ]
        );
    }
}