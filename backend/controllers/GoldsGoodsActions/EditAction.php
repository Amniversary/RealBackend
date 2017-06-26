<?php

namespace backend\controllers\GoldsGoodsActions;


use frontend\business\GoldsAccountUtil;
use frontend\business\GoldsGoodsUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;

use yii\base\Action;
/**
 * 修改金币商品
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class EditAction extends Action
{
    public function run($gold_goods_id){
        $model =   GoldsGoodsUtil::GetGoldGoodsModelOne($gold_goods_id);
        if(!isset($model)){
            ExitUtil::ExitWithMessage('金币商品不存在');
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save()){ 
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