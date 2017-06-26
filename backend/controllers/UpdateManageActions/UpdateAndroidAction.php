<?php

namespace backend\controllers\UpdateManageActions;


use backend\components\ExitUtil;
use frontend\business\CarouselUtil;
use frontend\business\UpdateContentUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * 安卓版本更新
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class UpdateAndroidAction extends Action
{
    public function run()
    {

        $module_id = 'android_version';
        $model = UpdateContentUtil::GetUpdateItemByModuleId($module_id);
        if(!isset($model))
        {
            $error = '更新记录不存在';
            ExitUtil::ExitWithMessage($error);
            \Yii::getLogger()->log($error.' :'.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
        }
        $error = '';
        $data = \Yii::$app->request->post('UpdateContent');
        if (isset($data))
        {
            if(!($model->load(\Yii::$app->request->post()) && $model->save()))
            {
                \Yii::$app->getSession()->setFlash('error','保存失败');
            }
            else
            {
                \Yii::$app->getSession()->setFlash('success','保存成功');
            }
        }

        return $this->controller->render('updateandroid', [
            'model' => $model,
            'error'=>$error,
        ]);
    }
} 