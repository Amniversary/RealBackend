<?php
namespace backend\controllers\PublicListActions;


use backend\models\AttentionMsgSearch;
use yii\base\Action;


class AttentionReplyAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '关注消息回复';
        $searchModel = new AttentionMsgSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('attentionevent',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
        ]);
    }
}