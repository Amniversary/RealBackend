<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/12
 * Time: 20:01
 */

namespace backend\controllers\ClientActions;


use backend\business\ClientInfoUtil;
use frontend\business\ClientUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UpdateClientInfoSaveByTrans;
use yii\base\Action;

class SetClientIdAction extends Action
{
    public function run($client_id)
    {
        $rst = ['code'=>'1','msg'=>''];
        if(empty($client_id))
        {
            $rst['message'] = '用户id不能为空';
            echo json_encode($rst);
            exit;
        }
        $client = ClientUtil::GetClientById($client_id);
        if(!isset($client))
        {
            $rst['message'] = '用户不存在';
            echo json_encode($rst);
            exit;
        }

        $swop_id = \Yii::$app->request->post('client_no');
        if(empty($swop_id))
        {
            $rst['msg'] = '用户账号参数错误';
            echo json_encode($rst);
            exit;
        }

        $swop_client = ClientUtil::GetClientNo($swop_id);
        if(!isset($swop_client))
        {
            $rst['msg'] = '交换用户信息不存在';
            echo json_encode($rst);
            exit;
        }

        $param = [
            'swop_id'=>$swop_client->client_id,
            'swop_no'=>$swop_client->client_no
        ];

        $transActions = new UpdateClientInfoSaveByTrans($client,$param);

        if(!$transActions->SaveRecordForTransaction($error,$out))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }

        $rst['code'] = '0';
        echo json_encode($rst);
    }
} 