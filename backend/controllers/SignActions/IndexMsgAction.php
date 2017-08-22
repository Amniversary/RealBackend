<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 下午5:04
 */

namespace backend\controllers\SignActions;


use backend\models\KeyWordMsgSearch;
use yii\base\Action;

class IndexMsgAction extends Action
{
    public function run()
    {
        $id = \Yii::$app->request->get('id');
        $this->controller->getView()->title = '签到消息';
        $searchModel = new KeyWordMsgSearch();
        $dataProvider = $searchModel->searchSignMsg(\Yii::$app->request->queryParams);
        return $this->controller->render('indexmsg',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'id'=>$id
        ]);
    }
}