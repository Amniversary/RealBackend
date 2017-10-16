<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 下午3:06
 */

namespace backend\controllers\LaterActions;


use common\models\AttentionEvent;
use common\models\LaterImage;
use common\models\LaterKeyword;
use common\models\LaterParams;
use common\models\SignKeyword;
use common\models\SignMessage;
use common\models\SignParams;
use yii\base\Action;
use yii\db\Exception;

class DeleteParamsAction extends Action
{
    public function run($id)
    {
        $rst = ['code'=> 1, 'msg'=>''];
        if(empty($id) || !isset($id)) {
            $rst['msg'] = '参数id 不能为空';
            echo json_encode($rst);exit;
        }
        $model = LaterParams::findOne(['id'=>$id]);
        if(!isset($model) || empty($model)) {
            $rst['msg'] = '配置记录不存在';
            echo json_encode($rst);exit;
        }
        try{
            $trans = \Yii::$app->db->beginTransaction();
            if(!$model->delete()) {
                $rst['msg'] = '删除失败';
                \Yii::error($rst['msg']. '  :' .var_export($model->getError(),true));
                echo json_encode($rst);exit;
            }
            LaterKeyword::deleteAll(['later_id'=>$id]);
            LaterImage::deleteAll(['later_id'=> $id]);
            $trans->commit();
        }catch(Exception $e) {
            $trans->rollBack();
            $rst['msg'] = $e->getMessage();
            echo json_encode($rst);exit;
        }

        $rst['code'] = 0;
        echo json_encode($rst);
    }

}