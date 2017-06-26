<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 15:22
 */

namespace backend\controllers\FamilyActions;


use backend\models\FamilySonSearch;
use yii\base\Action;

class IndexSonAction extends Action
{
    public function run($family_id,$page)
    {
        $this->controller->getView()->title = '家族成员管理';
        $searchModel = new FamilySonSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('index_son',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'family_id' => $family_id,
                'page' => $page
            ]
        );
    }
} 