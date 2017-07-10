<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/7
 * Time: 下午5:30
 */

namespace backend\controllers\CustomActions;


use backend\models\CustomSonSearch;
use yii\base\Action;

class IndexSonAction extends Action
{
    public function run()
    {
        $menu_id = \Yii::$app->request->get('menu_id');
        $this->controller->getView()->title = '自定义菜单';
        $searchModel = new CustomSonSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('indexson',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'menu_id'=>$menu_id
        ]);
    }
}