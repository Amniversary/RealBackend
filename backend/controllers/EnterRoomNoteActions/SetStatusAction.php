<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/6/6
 * Time: 10:00
 */

namespace backend\controllers\SystemMessageActions;


use common\models\SystemMessage;
use backend\business\SystemMessageUtil;
use yii\base\Action;

class SetStatusAction extends Action
{
    public function run($message_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($message_id))
        {
            $rst['msg']='消息id不能为空';
            echo json_encode($rst);
            exit;
        }
        $SystemMessage = SystemMessage::findOne(['message_id'=>$message_id]);
        if(!isset($SystemMessage))
        {
            $rst['msg']='消息不存在';
            echo json_encode($rst);
            exit;
        }
        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit))
        {
            $rst['message'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        if(empty($hasEdit))
        {
            $rst['message'] = '';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex))
        {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $modifyData = \Yii::$app->request->post('SystemMessage');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有SystemMessage模型对应的数据';
            echo json_encode($rst);
            exit;
        }

        if(!isset($modifyData[$editIndex]))
        {
            $rst['message'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }
        $dataItem = $modifyData[$editIndex];
        if(!isset($dataItem['status']))
        {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }
        $status = $dataItem['status'];
        $SystemMessage->status = $status;
        if(!SystemMessageUtil::SaveSystemMessage($SystemMessage,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
}