<?php

namespace backend\controllers\QiniuLivingActions;


use common\models\ClientLivingParameters;
use yii\base\Action;

/**
 * 新增用户参数
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateClientAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '新增用户参数';
        $model = new ClientLivingParameters();

        //$model->fps = 20;  //默认20
        //$model->video_bit_rate = 800;  //最小300  默认800

        if($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['client_params']);
        }
        else
        {
            return $this->controller->render('create_client', [
                    'model' => $model,
            ]);
        }
    }
} 