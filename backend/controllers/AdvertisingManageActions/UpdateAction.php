<?php

namespace backend\controllers\AdvertisingManageActions;


use backend\components\ExitUtil;
use common\models\AdImages;
use yii\base\Action;
/**
 * 修改弹窗广告图
 * Class UpdateAction
 */
class UpdateAction extends Action
{
    public function run($ad_id)
    {

        $model = AdImages::findOne(['ad_id' => $ad_id]);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('弹窗广告图记录不存在');
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            $sql = 'delete from mb_user_ad_images WHERE ad_id=:aid';
            $res = \Yii::$app->db->createCommand($sql,[':aid' => $ad_id])->execute();
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