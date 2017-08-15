<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/28
 * Time: 下午3:18
 */

namespace backend\controllers\BatchCustomActions;


use common\models\AuthorizationMenu;
use yii\base\Action;

class CreateMenuAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '新增菜单';
        $id = \Yii::$app->request->get('id');
        $model = new AuthorizationMenu();
        $model->global = $id;
        $model->parent_id = 0;
        $model->is_list = 0;
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
            return $this->controller->redirect(['index_menu','id'=>$id]);
        }else{
            return $this->controller->render('_form_menu',[
                'model'=>$model,
                'id'=>$id,
            ]);
        }
    }
}