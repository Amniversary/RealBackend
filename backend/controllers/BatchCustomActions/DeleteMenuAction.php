<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/28
 * Time: 下午3:36
 */

namespace backend\controllers\BatchCustomActions;


use common\models\AttentionEvent;
use common\models\AuthorizationMenu;
use yii\base\Action;
use yii\db\Exception;
use yii\db\Query;

class DeleteMenuAction extends Action
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
        $query = (new Query())
            ->select(['menu_id'])
            ->from('wc_authorization_menu')
            ->where(['parent_id'=>$menu_id])
            ->all();
        try{
            $trans = \Yii::$app->db->beginTransaction();
            if(!$model->delete()) {
                $rst['msg']='删除失败';
                \Yii::error('删除失败:'.var_export($model->getErrors(),true));
            }
            AuthorizationMenu::deleteAll(['parent_id'=>$model->menu_id]);
            AttentionEvent::deleteAll(['menu_id'=>$model->menu_id]);
            if(!empty($query)) {
                foreach($query as $list) {
                    AttentionEvent::deleteAll(['menu_id'=>$list['menu_id']]);
                }
            }
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