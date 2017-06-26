<?php

namespace backend\controllers\GoodsTicketToCashActions;


use backend\components\ExitUtil;
use frontend\business\GoodsTicketToCashUtil;
use yii\base\Action;
/**
 * 修改票提现商品
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class UpdateAction extends Action
{
    public function run($goods_id)
    {
        $model = GoodsTicketToCashUtil::GetGoodsTicketToCashById($goods_id);

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