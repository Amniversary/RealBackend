<?php

namespace backend\controllers\GiftActions;


use common\models\Gift;
use frontend\business\UpdateContentUtil;
use yii\base\Action;
use frontend\business\LivingUtil;
use frontend\business\FrontendCacheKeyUtil;
/**
 * 新增礼物
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new Gift();
        $this->controller->getView()->title = '新增礼物';
        $model ->order_no = '100';
        $model->remark2  = '0';
        if ($model->load(\Yii::$app->request->post()))
        {
            if(($model->special_effects == 0) && $model->world_gift == 2)
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