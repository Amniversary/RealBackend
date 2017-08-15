<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/9
 * Time: 下午7:56
 */

namespace backend\controllers\CustomActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use common\models\AuthorizationMenu;
use function Couchbase\fastlzCompress;
use yii\base\Action;

class CreateAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '新增菜单';
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $model = new AuthorizationMenu();
        $model->app_id = $cacheInfo['record_id'];
        $model->is_list = 0;
        $model->parent_id = 0;
        $model->type = 'view';
        if($model->load(\Yii::$app->request->post())){
            if($model->is_list == 1){
                $model->type = '';
                $model->url = '';
                $model->key_type = '';
            }
            if($model->type == 'view'){
                $model->key_type = '';
            }else{
                $model->url = '';
            }
            if(!$model->save()){
                \Yii::error('保存菜单失败：'.var_export($model->getErrors(),true));
                return false;
            }
            return $this->controller->redirect('index');
        }else{
            return $this->controller->render('_form',[
                'model'=>$model,
                'cache'=>$cacheInfo
            ]);
        }
    }
}