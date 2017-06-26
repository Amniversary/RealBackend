<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/18
 * Time: 15:24
 */

namespace backend\controllers\ClientActions;


use frontend\business\ClientUtil;
use yii\base\Action;

class ContractInfoAction extends Action
{
    public function run($client_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($client_id))
        {
            $rst['message'] = '用户id不能为空';
            echo json_encode($rst);
            exit;
        }
        $client = ClientUtil::GetClientById($client_id);
        if(!isset($client))
        {
            $rst['message'] = '用户记录不存在';
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
        $modifyData = \Yii::$app->request->post('Client');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有Goods模型对应的数据';
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
        if(!isset($dataItem['is_contract']))
        {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }
        $is_contract = $dataItem['is_contract'];
        $client->is_contract = $is_contract;
        ClientUtil::InsertClient($client,$error);
        if($is_contract == '2' && empty($client->cash_rite))
        {
            $client->cash_rite = '32';//默认签约率  32票换1元
        }
        if(!ClientUtil::SaveClient($client,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 