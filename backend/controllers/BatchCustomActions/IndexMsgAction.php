<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/14
 * Time: 下午5:24
 */

namespace backend\controllers\BatchCustomActions;


use backend\components\ExitUtil;
use backend\models\CustomMsgSearch;
use yii\base\Action;

class IndexMsgAction extends Action
{
    public function run($menu_id)
    {
        if(empty($menu_id)) {
            ExitUtil::ExitWithMessage('菜单Id 不能为空');
        }
        $id = \Yii::$app->request->get('id');
        $this->controller->getView()->title = '点击事件消息';
        $searchModel = new CustomMsgSearch();
        $dataProvider = $searchModel->searchBatch(\Yii::$app->request->queryParams);
        return $this->controller->render('indexmsg',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'menu_id' => $menu_id,
            'id' => $id
        ]);
    }
}