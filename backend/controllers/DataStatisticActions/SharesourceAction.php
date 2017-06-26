<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 15:48
 */
namespace backend\controllers\DataStatisticActions;

use backend\models\SharesourceSearch;
use yii\base\Action;

class SharesourceAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '主播分享统计表';
        $searchModel = new SharesourceSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('sharesource_index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }
}