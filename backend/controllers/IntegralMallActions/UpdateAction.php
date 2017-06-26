<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/21
 * Time: 17:48
 */
namespace backend\controllers\IntegralMallActions;


use backend\business\GoodsUtil;
use backend\business\LevelUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;
use common\models\IntegralMall;
use yii\base\Action;


class UpdateAction extends Action
{
    public function run($gift_order)
    {
        $model = IntegralMall::findOne(['gift_order'=>$gift_order]);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('商品信息不存在');
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