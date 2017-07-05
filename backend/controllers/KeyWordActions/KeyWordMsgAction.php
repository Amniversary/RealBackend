<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午3:25
 */

namespace backend\controllers\KeyWordActions;


use backend\business\WeChatUserUtil;
use backend\models\KeyWordMsgSearch;
use yii\base\Action;

class KeyWordMsgAction extends Action
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
        $this->controller->getView()->title = '关键词消息';
        $searchModel = new KeyWordMsgSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('indexmsg',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'is_verify'=>$is_verify
        ]);

    }
}