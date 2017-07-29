<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/10
 * Time: 下午12:21
 */

namespace backend\controllers\BatchCustomActions;


use backend\business\WeChatUserUtil;
use backend\components\ExitUtil;
use common\models\AuthorizationMenu;
use yii\base\Action;

class UpdateMenuAction extends Action
{
    public function run($menu_id,$id)
    {

        $model = AuthorizationMenu::findOne(['menu_id'=>$menu_id]);
        if(empty($model)){
            ExitUtil::ExitWithMessage('菜单记录不存在');
        }
        if(empty($model->type)) $model->type = 'view';
        if ($model->load(\Yii::$app->request->post())) {
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
                \Yii::error('更新菜单失败：'.var_export($model->getErrors(),true));
                ExitUtil::ExitWithMessage('更新菜单失败');
                exit;
            }
            return $this->controller->redirect(['index_menu']);
        } else {
            return $this->controller->render('_form_menu', [
                'model' => $model,
                'id'=> $id,
            ]);
        }
    }
}