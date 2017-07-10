<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/7
 * Time: 下午4:27
 */

namespace backend\controllers\CustomActions;


use backend\business\WeChatUserUtil;
use backend\models\CustomSearch;
use yii\base\Action;

class IndexAction extends Action
{
    public function run()
    {
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $is_verify = false;
        switch ($cacheInfo['verify_type_info']){
            case '0': $is_verify = true;break;
            case '3': $is_verify = true;break;
            case '4': $is_verify = true;break;
            case '5': $is_verify = true;break;
        }
        $this->controller->getView()->title = '自定义菜单';
        $searchModel = new CustomSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'is_verify'=>$is_verify
        ]);
    }
}