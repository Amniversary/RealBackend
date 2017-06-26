<?php
/**
 * Created by PhpStorm.
 * User: Amp
 * Date: 2016/11/6
 * Time: 1:47
 */

namespace backend\controllers\CheckMoneyGoodsActions;


use common\models\TicketToCash;
use frontend\business\JobUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;

class PlayBatchMoneyAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        //$record_id = \Yii::$app->request->post('check_id');
        $items = \Yii::$app->request->post('data');
        $refuesd_reason = \Yii::$app->request->post('refused_reason');
        $check_rst = \Yii::$app->request->post('check_res');
        $items = explode('-',$items);

        if(empty($items))
        {
            $rst['msg'] = '打款记录id参数不正确';
            echo json_encode($rst);
            exit;
        }
        $data = [];
        foreach($items as $l)
        {
            $tic = TicketToCashUtil::GetTicketToCashById($l);
            if(!isset($tic))
            {
                continue;
            }
            $tic->status = 5;
            if(!$tic->save())
            {
                continue;
            }
            $data[] = $l;
        }


        foreach($data as $li)
        {
            $record_id = $li;
            if(!TicketToCashUtil::CheckBaseInfo($record_id,$outinfo,$error))
            {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }

            $params = [
                'record_id'=>$record_id,
                'backend_user_id'=> \Yii::$app->user->id,
                'refuesd_reason'=>$refuesd_reason,
                'check_rst' => $check_rst,
                'user_id' => $outinfo['user_id'],
                'spbill_create_ip'=>$_SERVER['REMOTE_ADDR'],
            ];

            $jobServer = 'batchRechargeBeanstalk';
            if($outinfo['cash_type'] == 1)
            {
                //微信打款.
                $params['key_word'] = 'wechat_recharge';
                if(!JobUtil::AddCustomJob($jobServer,'wechat_batch_recharge',$params,$error))
                {
                    $rst['msg'] = $error;
                    echo json_encode($rst);
                    exit;
                }
            }
            else if($outinfo['cash_type'] == 2)
            {
                //支付宝
                $params['key_word'] = 'alipay_recharge';
                if(!JobUtil::AddCustomJob($jobServer,'alipay_batch_recharge',$params,$error))
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
        }
        $rst['code']='0';
        echo json_encode($rst);
    }
} 