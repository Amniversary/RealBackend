<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/20
 * Time: 14:25
 */

namespace backend\controllers\ClientActions;


use frontend\business\ClientUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;
use yii\log\Logger;

class CheckBatchAction extends Action
{
    public function run()
    {
        $ids = \Yii::$app->request->post('data');
        $check_rst = \Yii::$app->request->post('check_res');
        $ids = explode('-',$ids);

        if(empty($ids)){
            $rst['msg'] = '审核id不能空';
            echo json_encode($rst);
            exit;
        }

        //当  check_rst  1是通过  2是拒绝
        if(!isset($check_rst))
        {
            $rst['msg']='审核类型不能为空';
            echo json_encode($rst);
            exit;
        }

        $ids_len = count($ids);
        foreach($ids as $id)
        {

            $client = ClientUtil::GetClientById($id);

            if($client->status == 1)
            {
                if($client->client_type == 2)
                {
                    $rst['msg'] = '超级管理员不能被禁用';
                    echo json_encode($rst);
                    exit;
                }
            }

            if(!ClientUtil::BatchCheckCslient($client,$check_rst,$error))
            {
                \Yii::getLogger()->log('修改用户状态失败: '.$error,Logger::LEVEL_ERROR);
                $rst['msg'] = '修改用户状态失败';
                echo json_encode($rst);
                exit;
            }

            $ids_len--;

        }

        if(($ids_len>0) && ($ids_len === count($ids))){
            $rst['msg']='审核失败';
            echo json_encode($rst);
            exit;
        }

        $rst['code']='0';
        echo json_encode($rst);
    }
}