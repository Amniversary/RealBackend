<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 14:19
 */

namespace backend\controllers\ScoreGiftActions;


use backend\models\GiftScoreSearch;
use frontend\business\GiftUtil;
use yii\base\Action;

class ScoreIndexAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '礼物积分管理';
        $searchModel = new GiftScoreSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
            return $this->controller->render('scoreindex',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
} 