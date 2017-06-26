<?php

namespace backend\controllers\LuckyGiftActions;


use backend\components\ExitUtil;
use frontend\business\LuckyGiftUtil;
use yii\base\Action;
/**
 * 修改幸运礼物概率
 * Class UpdateAction
 * @package backend\controllers\UpdateAction
 */
class UpdateAction extends Action
{
    public function run($lucky_id)
    {
        $model = LuckyGiftUtil::GetLuckyGiftById($lucky_id);

        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('信息不存在');
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            LuckyGiftUtil::DeleteLuckyGiftCache();  //删除缓存
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