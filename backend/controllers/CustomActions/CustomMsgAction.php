<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/12
 * Time: 下午2:54
 */

namespace backend\controllers\CustomActions;


use backend\business\WeChatUserUtil;
use backend\components\ExitUtil;
use backend\models\CustomMsgSearch;
use yii\base\Action;

class CustomMsgAction extends Action
{
    public function run($menu_id)
    {
        if(empty($menu_id)) {
            ExitUtil::ExitWithMessage('菜单Id 不能为空');
        }
        //$cacheInfo = WeChatUserUtil::getCacheInfo();
        $this->controller->getView()->title = '点击事件消息';
        $searchModel = new CustomMsgSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->controller->render('indexmsg',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'menu_id' => $menu_id,
        ]);
    }
}