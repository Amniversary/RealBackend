<?php

namespace backend\controllers\GoodsActions;


use common\models\Goods;
use yii\base\Action;
use yii\log\Logger;

/**
 * 新增商品
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '新增商品';
        $model = new Goods();
        $model->status = 1;
        $model->sale_type = 4;
        $model->goods_type = 1;
        $model->order_no = '100';
        $model->extra_bean_num = 0;
        $model->pic = 'http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/bean.png';

        if($model->load(\Yii::$app->request->post()) && $model->save())
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