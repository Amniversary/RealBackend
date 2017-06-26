<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/28
 * Time: 10:20
 */

namespace backend\controllers\CheckMoneyGoodsActions;


use backend\business\CheckWeCatOrderForm;
use backend\business\UserUtil;
use common\components\alipay\AlipayUtil;
use common\components\IOSBuyUtil;
use common\components\wxpay\lib\WxPayApi;
use common\components\wxpay\lib\WxPayOrderQuery;
use common\components\wxpay\lib\WxPayOrderQueryApp;
use frontend\business\GoodsUtil;
use frontend\business\OtherPayUtil;
use frontend\business\RechargeListUtil;
use yii\base\Action;
use yii\log\Logger;

class CheckRechargeRecordAction extends Action
{
    public function run()
    {
        ini_set('memory_limit','10M');
        $rst = ['code'=>'1','msg'=>''];
        $pay_type = \Yii::$app->request->post('pay_type');
        $recharge_id = \Yii::$app->request->post('recharge_id');
        if(!isset($pay_type) && !isset($recharge_id))
        {
            $rst['msg'] = '获取充值参数信息错误';
            echo json_encode($rst);
            exit;
        }

        //$user_id = \Yii::$app->user->id;
        //$user = UserUtil::GetUserByUserId($user_id);
        $recharge_status = '';
        $recharge = RechargeListUtil::GetRachargeById($recharge_id);
        if($recharge->status_result == 1 || $recharge->status_result == 0) {
            switch ($pay_type) {
                case 3: //支付宝帐单
                    $recharge_status = AlipayUtil::QueryOrderStatus($recharge->pay_bill,'',$out);
                    break;

                case 4: //微信账单
                    $isOther = (strpos($recharge->pay_bill, 'ZHF-RGD') !== false);
                    $recharge_status = WxPayOrderQueryApp::CheckOrderAppResult($recharge->pay_bill,$out,$isOther);
                    //\Yii::getLogger()->log('recharge_status:'.$recharge_status,Logger::LEVEL_ERROR);
                    break;

                case 6: //苹果账单
                    $data = IOSBuyUtil::GetIosBuyVerify($recharge->remark2,false); //false 正式  true 测试
                    $recharge_rst = GoodsUtil::GetRechargeByOpReceiptData($data['trade_no']);
                    $recharge_status = $data['status'];
                    if(isset($recharge_rst) && $recharge_rst->status_result == 2)
                    {
                        $recharge_status = 5;
                    }
                    $iosbuygoods = require(\Yii::$app->getBasePath().'/../common/config/IosBuyGoodsList.php'); //苹果内购商品名称及价格对应信息
                    $goods_info = GoodsUtil::GetGoodsById($iosbuygoods[$data['total_fee']]);
                    $out = [
                        'trade_no'=>$data['trade_no'],
                        'total_fee'=>$goods_info->goods_price,
                    ];
                    break;

                case 100://web微信账单
                    $recharge_status = WxPayOrderQuery::CheckOrderResult($recharge->pay_bill,$out);
                    //\Yii::getLogger()->log('status:'.$recharge_status.' $out:'.var_export($out,true),Logger::LEVEL_ERROR);
                    break;

                default: //未知类型
                    break;
            }


            if (!RechargeListUtil::GetRechargeRecodeStatus($recharge,$recharge_status,$out, $pay_type, $error))
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