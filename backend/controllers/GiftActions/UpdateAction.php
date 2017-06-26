<?php

namespace backend\controllers\GiftActions;


use backend\components\ExitUtil;
use frontend\business\GiftUtil;
use frontend\business\UpdateContentUtil;
use yii\base\Action;
use frontend\business\FrontendCacheKeyUtil;
use frontend\business\LivingUtil;
/**
 * 修改票提现商品
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class UpdateAction extends Action
{
    public function run($gift_id,$page)
    {

        $model = GiftUtil::GetGiftById($gift_id);

        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('礼物不存在');
        }

        if ($model->load(\Yii::$app->request->post()))
        {
            if(($model->special_effects == 0) && ($model->world_gift == 2))
            {
                $model->addError('world_gift','连击礼物不能设置为世界礼物');
                return $this->controller->render('create', [
                    'model' => $model,
                ]);
            }

            if(($model->lucky_gift == 1) && ($model->gift_value < 10))
            {
                $model->addError('lucky_gift','幸运礼物豆值不能小于10');
                return $this->controller->render('create', [
                    'model' => $model,
                ]);
            }

            if(!$model->save())
            {
                return $this->controller->render('create', [
                    'model' => $model,
                ]);
            }else
            {
                $key = FrontendCacheKeyUtil::FRONTEND_V2_ZHIBOGETGIFTS_LIST_ALL;
                if( \Yii::$app->cache->get($key) ){
                    \Yii::$app->cache->delete($key);
                    $data = LivingUtil::GetGiftsList();
                    \Yii::$app->cache->set($key,$data);
                }
            }
            UpdateContentUtil::UpdateGiftVersion($error);
            return $this->controller->redirect(['index','page' => $page]);
        }
        else
        {
            return $this->controller->render('update', [
                'model' => $model,
                'page' => $page
            ]);
        }
    }
}