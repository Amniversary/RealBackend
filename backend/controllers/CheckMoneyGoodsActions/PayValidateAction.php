<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 11:08
 */

namespace backend\controllers\CheckMoneyGoodsActions;

use yii\base\Action;
use common\components\alipay\AlipayUtil;
use frontend\business\RechargeListUtil;
use common\models\Recharge;
use common\components\IOSBuyUtil;
use frontend\business\GoodsUtil;

class PayValidateAction extends Action
{
    public function run()
    {
        set_time_limit(0);
        $limit = (int)\Yii::$app->request->get('limit');
        $type = (int)\Yii::$app->request->get('pay_type');

        ob_flush();
        $query = Recharge::find();
        $query->limit($limit)
              ->andWhere(['status_result'=> '1','pay_type'=> $type])
              ->andWhere(['<','create_time',date('Y-m-d H:i:s',strtotime('-10 minutes'))])
              ->orderBy('create_time DESC');
        $trades = $query->all();
        $out = null;
        $error = null;


        $total = count($trades);
        echo "<script>parent.total($total);</script>";
        echo str_repeat(' ', 4096);

        foreach ($trades as $i => $trade) {
            $rst = 0;
            switch ($type) {
                case 3:
                    $rst = AlipayUtil::QueryOrderStatus($trade->pay_bill, '', $out);
                    break;
                case 6:
                    $data = IOSBuyUtil::GetIosBuyVerify($trade->remark2, false); //false 正式  true 测试
                    $rst = $data['status'];
                    $out = [
                        'trade_no' => $data['trade_no'],
                        'total_fee' => $data['total_fee'],
                    ];
            }

            $throw = [
                'index' => $i + 1,
                'type' => $rst,
                'msg'  => $rst,
                'recharge_id' => $trade->recharge_id,
                'pay_bill' => $trade->pay_bill,
                'goods_name' => $trade->goods_name,
                'pay_money' => $trade->pay_money,
                'create_time' => $trade->create_time,
                'user_id' => $trade->user_id,
                'pay_type' => $type,
                'other_msg' => json_encode($out),
            ];
            $throwJson = json_encode($throw);
            echo "<script>parent.longping($throwJson);</script>";
            echo str_repeat(' ', 4096);
            flush();
        }
    }
} 