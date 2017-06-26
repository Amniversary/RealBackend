<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\CheckMoneyGoodsActions;


use frontend\business\TicketToCashUtil;
use yii\base\Action;

class CheckBatchMoneyGoodsActions extends Action
{
    public function run()
    {
        $ids = \Yii::$app->request->post('data');
        $refuesd_reason = \Yii::$app->request->post('refused_reason');
        $check_rst = \Yii::$app->request->post('check_res');

        $ids = explode('-',$ids);
        if(empty($ids))
        {
            $rst['msg'] = '审核id不能空';
            echo json_encode($rst);
            exit;
        }

        if(!isset($check_rst) || !in_array($check_rst,[2,3,4,6]))
        {
            $rst['msg']='审核结果值异常';
            echo json_encode($rst);
            exit;
        }

        if(($check_rst == 4 || $check_rst == 6) && empty($refuesd_reason)){
            $rst['msg']='拒绝原因不能为空';
            echo json_encode($rst);
            exit;
        }

        $ids_len = count($ids);

        foreach($ids as $id)
        {
            $params = [
                'record_id'=>$id,
                'backend_user_id'=> \Yii::$app->user->id,
                'refuesd_reason'=>$refuesd_reason,
                'check_rst' => $check_rst
            ];

            if(!TicketToCashUtil::CheckRefuse($params,$error))
            {
                continue;
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