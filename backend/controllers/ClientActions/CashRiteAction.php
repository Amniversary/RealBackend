<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/24
 * Time: 9:38
 */

namespace backend\controllers\ClientActions;


use frontend\business\ClientUtil;
use yii\base\Action;

class CashRiteAction extends Action
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
            $rst['message'] = '没有Client模型对应的数据';
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
        if(!isset($dataItem['cash_rite']))
        {
            $rst['message'] = '签约率为空';
            echo json_encode($rst);
            exit;
        }
        $cash_rite = $dataItem['cash_rite'];
        $client->cash_rite = $cash_rite;
        if(!ClientUtil::SaveClient($client,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 