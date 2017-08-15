<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/12
 * Time: 下午2:54
 */

namespace backend\controllers\BatchCustomActions;


use backend\business\WeChatUserUtil;
use backend\components\ExitUtil;
use backend\models\CustomMsgSearch;
use yii\base\Action;

class IndexSonMsgAction extends Action
{
    public function run($menu_id)
    {
        if(empty($menu_id)) {
            ExitUtil::ExitWithMessage('菜单Id 不能为空');
        }
        $id = \Yii::$app->request->get('id');
        $parent_id = \Yii::$app->request->get('parent_id');
        $this->controller->getView()->title = '点击事件消息';
        $searchModel = new CustomMsgSearch();
        $dataProvider = $searchModel->searchBatch(\Yii::$app->request->queryParams);
        return $this->controller->render('indexsonmsg',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'menu_id' => $menu_id,
            'parent_id' => $parent_id,
            'id'=>$id
        ]);
    }
}