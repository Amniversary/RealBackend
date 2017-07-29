<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/28
 * Time: 下午2:27
 */

namespace backend\controllers\BatchCustomActions;


use backend\models\CustomSearch;
use yii\base\Action;

class IndexMenuAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '自定义菜单';
        $id = \Yii::$app->request->get('id');
        $searchModel = new CustomSearch();
        $dataProvider = $searchModel->searchMenu(\Yii::$app->request->queryParams);
        return $this->controller->render('indexmenu',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'id'=>$id
        ]);
    }
}