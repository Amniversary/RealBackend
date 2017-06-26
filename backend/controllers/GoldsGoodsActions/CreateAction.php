<?php

namespace backend\controllers\GoldsGoodsActions;


use common\models\GoldsGoods;
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
        $this->controller->getView()->title = '新增金币商品';
        $model = new GoldsGoods();
        $model->status = 1;
        $model->sale_type = 4;
        $model->gold_goods_type = 1;
        $model->order_no = '100';
        $model->extra_integral_num = 0;
        $model->gold_goods_pic = 'http://oss-cn-hangzhou.aliyuncs.com/mblive/meibo-test/bean.png';

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