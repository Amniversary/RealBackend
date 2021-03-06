<?php
namespace backend\controllers\PublicListActions;


use backend\business\WeChatUserUtil;
use backend\models\AttentionMsgSearch;
use yii\base\Action;


class AttentionReplyAction extends Action
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
        $this->controller->getView()->title = '关注消息回复';
        $searchModel = new AttentionMsgSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('attentionevent',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'is_verify'=>$is_verify
        ]);
    }
}