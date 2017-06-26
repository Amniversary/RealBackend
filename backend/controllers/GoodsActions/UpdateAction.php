<?php

namespace backend\controllers\GoodsActions;


use backend\business\GoodsUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;
use yii\base\Action;
/**
 * 修改人员
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class UpdateAction extends Action
{
    public function run($goods_id)
    {
        $model = GoodsUtil::GetGoodsById($goods_id);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('商品不存在');
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