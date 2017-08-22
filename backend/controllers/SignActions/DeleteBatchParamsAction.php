<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/22
 * Time: 下午12:21
 */

namespace backend\controllers\SignActions;


use common\models\AttentionEvent;
use common\models\SignKeyword;
use common\models\SignMessage;
use common\models\SignParams;
use yii\base\Action;
use yii\db\Exception;


class DeleteBatchParamsAction extends Action
{
    public function run($id)
    {
        $rst = ['code'=> 1, 'msg'=>''];
        if(empty($id) || !isset($id)) {
            $rst['msg'] = '参数id 不能为空';
            echo json_encode($rst);exit;
        }
        $model = SignParams::findOne(['id'=>$id]);
        if(!isset($model) || empty($model)) {
            $rst['msg'] = '签到日期配置记录不存在';
            echo json_encode($rst);exit;
        }
        $message = SignMessage::findAll(['sign_id'=>$model->id]);
        try{
            $trans = \Yii::$app->db->beginTransaction();
            if(!$model->delete()) {
                $rst['msg'] = '删除失败';
                \Yii::error($rst['msg']. '  :' .var_export($model->getError(),true));
                echo json_encode($rst);exit;
            }
            SignKeyword::deleteAll(['sign_id'=>$model->id]);
            SignMessage::deleteAll(['sign_id'=>$model->id]);
            foreach( $message as $item ) {
                AttentionEvent::deleteAll(['record_id'=>$item['msg_id']]);
            }
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