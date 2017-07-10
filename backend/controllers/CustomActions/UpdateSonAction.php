<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/10
 * Time: 下午1:41
 */

namespace backend\controllers\CustomActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use backend\components\ExitUtil;
use common\models\AuthorizationMenuSon;
use yii\base\Action;

class UpdateSonAction extends Action
{
    public function run($record_id)
    {
        $model = AuthorizationMenuSon::findOne(['record_id'=>$record_id]);
        if(empty($model)){
             ExitUtil::ExitWithMessage('子菜单记录不存在');
        }
        $cache = WeChatUserUtil::getCacheInfo();
        if(empty($model->type)) $model->type = 'view';
        if ($model->load(\Yii::$app->request->post())) {
            if($model->type == 'click'){
                $model->url = '';
            }else{
                $model->key_type = '';
            }
            if(!$model->save()){
                \Yii::error('更新子菜单失败：'.var_export($model->getErrors(),true));
                ExitUtil::ExitWithMessage('更新菜单失败');
                exit;
            }
            return $this->controller->redirect(['indexson',['menu_id'=>$model->menu_id]]);
        } else {
            return $this->controller->render('_formson', [
                'model' => $model,
                'cache' => $cache
            ]);
        }
    }
}