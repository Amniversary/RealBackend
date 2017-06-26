<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28
 * Time: 16:46
 */

namespace backend\controllers\ActivityShareActions;


use backend\components\ExitUtil;
use frontend\business\ShareUtil;
use yii\base\Action;

class UpdateAction extends Action
{
    public function run($share_id)
    {
        $model = ShareUtil::GetShareInfo($share_id);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('分享信息不存在');
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            return $this->controller->redirect(['index']);
        }
        else
        {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }
} 