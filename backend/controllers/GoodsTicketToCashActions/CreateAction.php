<?php

namespace backend\controllers\GoodsTicketToCashActions;


use common\models\GoodsTicketToCash;
use yii\base\Action;
/**
 * 新增票提现商品
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new GoodsTicketToCash();
        $this->controller->getView()->title = '新增提现商品';
        $model->status = 1;
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
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