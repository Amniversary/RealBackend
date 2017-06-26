<?php

namespace backend\controllers\CheckMoneyGoodsActions;


use frontend\business\TicketToCashUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * 打款拒绝、通过
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class CheckRefuseAction extends Action
{
    public function run()
    {
        $error = '';
        $record_id = \Yii::$app->request->post('check_id');
        $refuesd_reason = \Yii::$app->request->post('refused_reason');
        $check_rst = \Yii::$app->request->post('check_res');

        if(!TicketToCashUtil::CheckBaseInfo($record_id,$outinfo,$error)){
            $rst['msg']=$error;
            echo json_encode($rst);
            exit;
        }
        if(!isset($check_rst) || !in_array($check_rst,[2,3,4,6]))
        {
            $rst['msg']='审核结果值异常';
            echo json_encode($rst);
            exit;
        }

        $params = [
            'record_id'=>$record_id,
            'backend_user_id'=> \Yii::$app->user->id,
            'refuesd_reason'=>$refuesd_reason,
            'check_rst' => $check_rst
        ];
        if(!TicketToCashUtil::CheckRefuse($params,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        echo json_encode($rst);

    }
}