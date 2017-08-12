<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/7
 * Time: 下午7:34
 */

namespace backend\controllers\CustomActions;


use common\models\AttentionEvent;
use common\models\AuthorizationMenu;
use common\models\AuthorizationMenuSon;
use yii\base\Action;
use yii\base\Exception;

class DeleteAction extends Action
{
    public function run($menu_id)
    {
        $rst = ['code'=>'1', 'msg'=>''];
        if(empty($menu_id)){
            $rst['msg'] = '菜单记录Id不能为空';
            echo json_encode($rst);
            exit;
        }

        $model = AuthorizationMenu::findOne(['menu_id'=>$menu_id]);
        if(empty($model)){
            $rst['msg'] = '菜单记录不存在或已经删除';
            echo json_encode($rst);
            exit;
        }
        try{
            $trans = \Yii::$app->db->beginTransaction();
            if(!$model->delete()) {
                $rst['msg']='删除失败';
                \Yii::error('删除失败:'.var_export($model->getErrors(),true));
            }
            AuthorizationMenuSon::deleteAll(['menu_id'=>$model->menu_id]);
            AttentionEvent::deleteAll(['menu_id'=>$menu_id]);
            $trans->commit();
        } catch (Exception $e){
            $trans->rollBack();
            $rst['msg'] = $e->getMessage();
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
}