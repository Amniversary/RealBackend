<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/10/19
 * Time: 9:00
 */

namespace backend\controllers\GoldsGoodsActions;

use backend\models\GoldsGoodsSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run(){
        $this->controller->getView()->title = '金币商品管理';
        $searchModel = new GoldsGoodsSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
}