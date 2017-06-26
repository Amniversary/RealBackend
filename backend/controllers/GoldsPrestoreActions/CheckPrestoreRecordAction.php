<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 10:20
 */

namespace backend\controllers\GoldsPrestoreActions;


use backend\business\CheckWeCatOrderForm;
use backend\business\UserUtil;

use common\components\alipay\AlipayUtil;
use common\components\IOSBuyUtil;
use common\components\wxpay\lib\WxPayApi;
use common\components\wxpay\lib\WxPayOrderQuery;
use common\components\wxpay\lib\WxPayOrderQueryApp;

use frontend\business\OtherPayUtil;
use frontend\business\RechargeListUtil;
use frontend\business\GoldsPrestoreUtil;

use yii\base\Action;
use yii\log\Logger;

class CheckPrestoreRecordAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        $pay_type = \Yii::$app->request->post('pay_type');
        $prestore_id = \Yii::$app->request->post('prestore_id');
        if(!isset($pay_type) && !isset($prestore_id))
        {
            $rst['msg'] = '获取充值参数信息错误';
            echo json_encode($rst);
            exit;
        }

        $user_id = \Yii::$app->user->id;
        //$user = UserUtil::GetUserByUserId($user_id);
        $prestore_status = '';
        //$recharge = RechargeListUtil::GetRachargeById($recharge_id);
        $goldPrestore = GoldsPrestoreUtil::GetGoldPrestoreModelById($prestore_id);
        if($goldPrestore->status_result == 1) {
            switch ($pay_type) {
                case 3: //支付宝帐单
                    $prestore_status = AlipayUtil::QueryOrderStatus($goldPrestore->pay_bill,'',$out);
                    break;

                case 4: //微信账单
                    $isOther = (strpos($goldPrestore->pay_bill, 'ZHF-RGD') !== false);
                    $prestore_status = WxPayOrderQueryApp::CheckOrderAppResult($goldPrestore->pay_bill,$out,$isOther);
                    break;

                case 6: //苹果账单
                    $data = IOSBuyUtil::GetIosBuyVerify($goldPrestore->remark2,false); //false 正式  true 测试
                    $prestore_status = $data['status'];
                    $out = [
                        'trade_no'=>$data['trade_no'],
                        'total_fee'=>$data['total_fee'],
                    ];
                    break;

                case 100://web微信账单
                    $prestore_status = WxPayOrderQuery::CheckOrderResult($goldPrestore->pay_bill,$out);
                    //\Yii::getLogger()->log('status:'.$recharge_status.' $out:'.var_export($out,true),Logger::LEVEL_ERROR);
                    break;
                default: //未知类型
                    break;
            }
            //RechargeListUtil::GetRechargeRecodeStatus($recharge,$recharge_status,$out, $pay_type, $error)
            if (!GoldsPrestoreUtil::GetPrestoreRecodeStatus($goldPrestore, $prestore_status, $out, $pay_type, $error))
            {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
        }
        $rst['code'] = '0';
        echo json_encode($rst);
    }
} 