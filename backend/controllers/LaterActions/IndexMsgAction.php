<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/11
 * Time: 下午5:25
 */

namespace backend\controllers\LaterActions;


use backend\models\KeyWordMsgSearch;
use backend\models\LaterImageSearch;
use backend\models\LaterSearch;
use yii\base\Action;

class IndexMsgAction extends Action
{
    public function run()
    {
        $id = \Yii::$app->request->get('id');
        $this->controller->getView()->title = '签到消息';
        $searchModel = new LaterImageSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index_msg',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'id'=>$id
        ]);
    }
}