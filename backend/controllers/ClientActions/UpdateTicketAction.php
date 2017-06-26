<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/26
 * Time: 11:12
 */

namespace backend\controllers\ClientActions;


use frontend\business\BalanceUtil;
use yii\base\Action;
use yii\log\Logger;

class UpdateTicketAction extends Action
{
    public function run($client_id)
    {
        $rst = ['code'=>'1','msg'=>''];
        if(empty($client_id))
        {
            $rst['msg'] = '用户id 不能为空';
            echo json_encode($rst);
            exit;
        }
        $balance = BalanceUtil::GetUserBalanceById($client_id);
        if(!isset($balance))
        {
            $rst['msg'] = '用户账户信息不存在!';
            echo json_encode($rst);
            exit;
        }


        $op_money = \Yii::$app->request->post('op_money');
        $operate_type = \Yii::$app->request->post('operate_type');

        if(empty($op_money) || empty($operate_type))
        {
            $rst['msg'] = '请求的对应参数错误';
            echo json_encode($rst);
            exit;
        }

        switch($operate_type)
        {
            case 1:
                if(!BalanceUtil::AddReadBeanNum($client_id,$op_money,$error))
                {
                    $rst['msg'] = $error;
                    echo json_encode($rst);
                    exit;
                }
                break;
            case 2:
                if(!BalanceUtil::SubReadBeanNum($client_id,$op_money,$error))
                {
                    $rst['msg'] = $error;
                    echo json_encode($rst);
                    exit;
                }
                break;
            case 3:
                if(!BalanceUtil::AddVirtualBeanNum($client_id,$op_money,$error))
                {
                    $rst['msg'] = $error;
                    echo json_encode($rst);
                    exit;
                }
                break;
            case 4:
                if(!BalanceUtil::SubVirtualBeanNum($client_id,$op_money,$error))
                {
                    $rst['msg'] = $error;
                    echo json_encode($rst);
                    exit;
                }
                break;
            case 5:
                if(!BalanceUtil::AddUserTicketNum($client_id,$op_money,$error))
                {
                    $rst['msg'] = $error;
                    echo json_encode($rst);
                    exit;
                }
                break;
            case 6:
                if(!BalanceUtil::SubUserTicketNum($client_id,$op_money,$error))
                {
                    $rst['msg'] = $error;
                    echo json_encode($rst);
                    exit;
                }
                break;
        }

        $rst['code'] = '0';
        echo json_encode($rst);
    }
} 