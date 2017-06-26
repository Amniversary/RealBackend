<?php

namespace backend\controllers\QiniuLivingActions;


use backend\business\LivingParameterUtil;
use backend\components\ExitUtil;
use yii\base\Action;
/**
 * 修改直播参数
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class UpdateAction extends Action
{
    public function run($quality_id)
    {
        $model = LivingParameterUtil::GetLivingParameterById($quality_id);

        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('参数不存在');
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['living_parameters']);
        }
        else
        {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }
}