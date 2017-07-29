<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/10
 * Time: 下午12:02
 */

namespace backend\controllers\BatchCustomActions;


use common\models\AuthorizationMenuSon;
use yii\base\Action;

class DeleteSonAction extends Action
{
    public function run($record_id)
    {
        $rst = ['code'=>'1', 'msg'=>''];
        if(empty($record_id)){
            $rst['msg'] = '子菜单记录Id不能为空';
            echo json_encode($rst);
            exit;
        }

        $model = AuthorizationMenuSon::findOne(['record_id'=>$record_id]);
        if(empty($model)){
            $rst['msg'] = '子菜单记录不存在或已经删除';
            echo json_encode($rst);
            exit;
        }

        if(!$model->delete()) {
            $rst['msg']='删除失败';
            \Yii::error('删除失败:'.var_export($model->getErrors(),true));
        }

        $rst['code'] = '0';
        echo json_encode($rst);
    }
}