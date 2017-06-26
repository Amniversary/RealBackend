<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 13:23
 */
namespace backend\controllers\AdvertorialActions;


use backend\business\AdvertorialUtil;
use backend\components\ExitUtil;
use yii\base\Action;



class UpdateAction extends Action
{
    public function run($record_id)
    {
        $model = AdvertorialUtil::AdvertorialById($record_id);

        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('参数不存在');
        }


        if ($model->load(\Yii::$app->request->post()) )
        {

            if($model->save())
            {
                return $this->controller->redirect(['index']);
            }
        }
        else
        {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }
}