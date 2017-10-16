<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 上午11:17
 */

namespace backend\controllers\SignActions;


use backend\business\WeChatUserUtil;
use backend\models\BatchKeywordSearch;
use backend\models\KeyWordSearch;
use yii\base\Action;

class KeywordAction  extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '签到关键字设置';
        $cache = WeChatUserUtil::getCacheInfo();
        $is_verify = false;
        if(in_array($cache['verify_type_info'], [0, 3, 4, 5])) {
            $is_verify = true;
        }
        $searchModel = new BatchKeywordSearch();
        $dataProvider = $searchModel->searchSign(\Yii::$app->request->queryParams);
        return $this->controller->render('keyword', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'is_verify' => $is_verify
        ]);
    }
}