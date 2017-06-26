<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/2
 * Time: 19:38
 */

namespace backend\controllers\CloseLivingActions;


use backend\models\WeChatLiveOffSearch;
use yii\base\Action;

class WeChatLiveOffAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '微信直播开关记录';
        $searchModel = new WeChatLiveOffSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('wechatliving',
            [
                'searchModel'=>$searchModel,
                'dataProvider'=>$dataProvider
            ]
        );
    }
} 