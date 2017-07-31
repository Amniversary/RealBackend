<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/28
 * Time: 下午2:22
 */

namespace backend\controllers\BatchCustomActions;


use common\models\AuthorizationMenu;
use common\models\AuthorizationMenuSon;
use common\models\MenuList;
use common\models\SystemMenu;
use yii\base\Action;
use yii\db\Exception;
use yii\db\Query;

class DeleteAction extends Action
{
    public function run($id)
    {
        $rst = ['code'=>'1', 'msg'=>''];
        if(empty($id)){
            $rst['msg'] = '菜单配置记录Id不能为空';
            echo json_encode($rst);
            exit;
        }
        $model = SystemMenu::findOne(['id'=>$id]);
        if(empty($model)){
            $rst['msg'] = '菜单配置记录不存在或已经删除';
            echo json_encode($rst);
            exit;
        }
        $query = (new Query())
            ->select(['menu_id'])
            ->from('wc_authorization_menu')
            ->where(['global'=>$id])->all();
        try{
            $trans = \Yii::$app->db->beginTransaction();
            if($model->delete() === false) {
                $rst['msg']='删除失败';
                \Yii::error('删除失败:'.var_export($model->getErrors(),true));
                echo json_encode($rst);
                exit;
            }
            MenuList::deleteAll(['deploy_id'=>$id]);
            AuthorizationMenu::deleteAll(['global'=>$id]);
            foreach($query as $item) {
                AuthorizationMenuSon::deleteAll(['menu_id'=>$item['menu_id']]);
            }
            $trans->commit();
        }catch(Exception $e){
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