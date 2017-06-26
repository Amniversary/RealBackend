<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 上午11:30
 */

namespace frontend\zhiboapi\v1;

use frontend\business\GoodsUtil;
use common\components\IOSBuyUtil;
use common\models\ReceiptData;
use common\models\Recharge;
use common\components\UsualFunForStringHelper;
use frontend\business\BalanceUtil;
use frontend\business\ClientUtil;
use frontend\business\MultiUpdateContentUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RechargeListRecordSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RechargeSaveByTrans;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;


/**
 * Class 苹果内购接口(仅用于ios)
 * @package frontend\zhiboapi\v3
 */
class ZhiBoIosBuyVerify implements IApiExcute
{

    /**
     * 苹果内购接口(仅用于ios)
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData, &$error, $extendData= array())
    {
        $error = '';
        $data  = $dataProtocal['data'];
        $uniqueNo    = isset($data['unique_no']) ? $data['unique_no'] : null;
        $receiptData = isset($data['receipt-data']) ? $data['receipt-data'] : null;

        // 验证用户
        $user = ClientUtil::GetUserByUniqueId($uniqueNo);
        if (empty($user)) {
            $error = '用户信息不存在';
            return false;
        }

        // 保存用户提交的 receipt-data，防止丢失
        $receiptDataModel = new ReceiptData();
        $receiptDataModel->setAttributes([
            'table_name'   => 'mb_client',
            'table_row_id' => $user->client_id,
            'data'         => $receiptData,
            'data_hash'    => md5($receiptData),
            'create_date'  => date('Y-m-d H:i:s'),
        ]);
        $receiptDataModel->save();

        // 是否使用苹果订单验证沙盒
        $isSandbox = MultiUpdateContentUtil::CheckVersionInCheck(
            $dataProtocal['app_id'],
            'ios_version',
            $dataProtocal['app_version_inner']
        );

        // 苹果订单验证
        $resultData = IOSBuyUtil::GetIosBuyVerify($receiptData, $isSandbox);
        if ($resultData['status'] != 1) {
            $error = ['errno'=>'11013','errmsg'=>'订单验证失败'];
            return false;
        }

        // 验证商品，商品ID为苹果订单验证返回的 product_id 在配置中对应的ID
        $config = require(\Yii::$app->getBasePath() . '/../common/config/IosBuyGoodsList.php');
        $apGoodId = $resultData['total_fee'];  // 苹果返回的 product_id
        $goodId = isset($config[$apGoodId]) ? $config[$apGoodId] : null;  // 配置中数据库表中对应的id
        $goodModel = GoodsUtil::GetGoodsById($goodId);
        if (empty($goodModel)) {
            $error = '未找到对应商品';
            return false;
        }

        // 验证验证返回订单号是否已经完成支付
        // 如果对应的订单已经存在，并且未完成，则继续支付
        $rechargeModel = Recharge::findOne([
            'other_pay_bill' => $resultData['trade_no'],
            'pay_type'       => 6
        ]);
        if ($rechargeModel) {
            // 完成支付，防止重复刷单
            if ($rechargeModel->status_result != 1) {
                $error = $error = ['errno'=>'11013','errmsg'=>'订单已经完成'];
                return false;
            }
        } else {
            // 保存订单信息 mb_recharge
            $insertTransActions = [
                new RechargeSaveByTrans($goodModel, $resultData['trade_no'], $user->client_id, 1, '', $receiptDataModel->id)
            ];
            if (!SaveByTransUtil::RewardSaveByTransaction($insertTransActions, $error, $outInfo)) {
                return false;
            }
            $rechargeModel = Recharge::findOne([
                'recharge_id' => $outInfo['relate_id']
            ]);
        }

        // 修改余额
        $balanceModel = BalanceUtil::GetUserBalanceByUserId($user->client_id);
        $transActions = [
            // 修改订单信息 mb_recharge
            new RechargeListRecordSaveByTrans($rechargeModel, [
                'other_pay_bill' => $rechargeModel->other_pay_bill,  // 苹果返回的订单编号
                'user_name'      => $user->nick_name  // 用户的昵称
            ]),
            // 修改账户余额 mb_balance
            new ModifyBalanceByAddRealBean($balanceModel, [
                'bean_num' => $rechargeModel->bean_num  // 充值的金额
            ]),
            // 修改财务日志 mb_client_balance_log
            new CreateUserBalanceLogByTrans($balanceModel, [
                'unique_id'   => UsualFunForStringHelper::CreateGUID(),  //
                'device_type' => 3,  // 设备类型，统一为其它
                'op_value'    => $rechargeModel->bean_num,  // 充值的金额
                'relate_id'   => $rechargeModel->recharge_id,  // 充值记录表id mb_recharge
                'field'       => 'bean_balance',  // 记录类型
                'operate_type'=> 1  // 操作类型 1为 充值，豆增加
            ])
        ];
        if (!SaveByTransUtil::RewardSaveByTransaction($transActions, $error, $outInfo)) {
            return false;
        }

        $error = '购买成功';
        $rstData['data']['bean_num'] = $rechargeModel->bean_num;
        return true;
    }
}
