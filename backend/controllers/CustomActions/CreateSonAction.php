<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/10
 * Time: 上午11:11
 */

namespace backend\controllers\CustomActions;


use backend\business\WeChatUserUtil;
use common\models\AuthorizationMenu;
use common\models\AuthorizationMenuSon;
use yii\base\Action;

class CreateSonAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '新增子菜单';
        $menu_id = \Yii::$app->request->get('menu_id');
        $menuInfo = AuthorizationMenu::findOne(['menu_id'=>$menu_id]);
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $model = new AuthorizationMenuSon();
        $model->menu_id = $menu_id;
        $model->type = 'view';
        if($model->load(\Yii::$app->request->post())){
            if($model->type == 'click'){
                $model->url = '';
            }else{
                $model->key_type = '';
            }
            if(!$model->save()){
                \Yii::error('保存子菜单失败：'. var_export($model->getErrors(),true));
                return false;
            }
            return $this->controller->redirect(['indexson','menu_id'=>$menu_id]);
        }else{
            return $this->controller->render('_formson',[
                'model'=>$model,
                'cache'=>$cacheInfo,
                'menuinfo'=>$menuInfo
            ]);
        }
    }
}