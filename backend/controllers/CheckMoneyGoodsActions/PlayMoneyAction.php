<?php

namespace backend\controllers\CheckMoneyGoodsActions;


use frontend\business\JobUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * 打款
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class PlayMoneyAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        $record_id = \Yii::$app->request->post('check_id');
        //$items = \Yii::$app->request->post('data');
        $refuesd_reason = \Yii::$app->request->post('refused_reason');
        $check_rst = \Yii::$app->request->post('check_res');
        //$items = explode('-',$items);

        if(empty($record_id))
        {
            $rst['msg'] = '打款记录id参数不正确';
            echo json_encode($rst);
            exit;
        }
        if(!TicketToCashUtil::CheckBaseInfo($record_id,$outinfo,$error))
        {
            $rst['msg']=$error;
            echo json_encode($rst);
            exit;
        }

        if(!isset($check_rst) || !in_array($check_rst,[2,3,4]))
        {
            $rst['msg']='打款结果值异常';
            echo json_encode($rst);
            exit;
        }

        if(in_array($outinfo['status'],[3,4]))
        {
            $rst['msg']='该记录已经处理过了';
            echo json_encode($rst);
            exit;
        }
        $params = [
            'record_id'=>$record_id,
            'backend_user_id'=> \Yii::$app->user->id,
            'refuesd_reason'=>$refuesd_reason,
            'check_rst' => $check_rst,
            'user_id' => $outinfo['user_id'],
            'spbill_create_ip'=>$_SERVER['SERVER_ADDR'],
        ];
        //$jobServer = 'batchRechargeBeanstalk';
        if($outinfo['cash_type'] == 1)
        {
            //微信打款.
            $bind_wechat_info = TicketToCashUtil::CheckBindWeChat($record_id);
            if(empty($bind_wechat_info))
            {
                $rst['msg']='该用户未绑定微信';
                echo json_encode($rst);
                exit;
            }
            $params['other_id'] = $bind_wechat_info['other_id'];
            if(!TicketToCashUtil::SaveWeChatTicketToCash($params,$error))
            {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
        }
        else if($outinfo['cash_type'] == 2)
        {
            //支付宝

            $bind_alipay_info = TicketToCashUtil::CheckBindAlipay($record_id);
            if(empty($bind_alipay_info))
            {
                $rst['msg']='该用户未绑定支付宝';
                echo json_encode($rst);
                exit;
            }

            if(!TicketToCashUtil::SaveAlipayTicketToCash($params,$error))
            {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
        }
        else
        {
            $rst['msg'] = '提现方式非支付宝或微信';
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        echo json_encode($rst);
    }
}