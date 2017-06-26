<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26
 * Time: 9:49
 */

namespace backend\controllers\ActivityPrizeActions;


use backend\models\ActivityPrizeSendSearch;
use yii\base\Action;

class ActivityPrizeSendAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '活动中奖记录';

        $searchModel = new ActivityPrizeSendSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index_prize_record',
            [
                'searchModel' => $searchModel,
                'dataProvider' =>$dataProvider
            ]
        );
    }
} 