<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/6
 * Time: 下午3:58
 */

namespace backend\controllers\PublicListActions;


use backend\business\AuthorizerUtil;
use common\models\AttentionEvent;
use yii\base\Action;

class OrderNoAction extends Action
{
    public function run($record_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($record_id)) {
            $rst['message'] = '消息id 不能为空';
            echo json_encode($rst);
            exit;
        }
        $data = AttentionEvent::findOne(['record_id'=>$record_id]);
        if(!isset($data)) {
            $rst['message'] = '消息记录不存在';
            echo json_encode($rst);
            exit;
        }
        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit)) {
            $rst['message'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex)) {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $modifyData = \Yii::$app->request->post('AttentionEvent');
        if(!isset($modifyData)) {
            $rst['message'] = '没有User模型对应的数据';
            echo json_encode($rst);
            exit;
        }

        if(!isset($modifyData[$editIndex])) {
            $rst['message'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }
        $dataItem = $modifyData[$editIndex];
        if(!isset($dataItem['order_no'])) {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }
        $status = $dataItem['order_no'];

        $data->order_no = $status;
        if(!AuthorizerUtil::SaveAttentionEven($data, $error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
}