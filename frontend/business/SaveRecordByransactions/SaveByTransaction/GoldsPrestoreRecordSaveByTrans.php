<?php
/**
 * Created by PhpStorm.
 * User: WangWei
 * Date: 2016/10/10
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Recharge;
use common\models\GoldsAccount;
use common\models\IntegralAccount;
use common\models\GoldsAccountLog;
use common\models\GoldsPrestore;
use common\models\GoldsGoods;
use frontend\business\GoldsAccountUtil;
use frontend\business\GoldsAccountLogUtil;
use frontend\business\GoldsPrestoreUtil;
use frontend\business\IntegralAccountUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class GoldsPrestoreRecordSaveByTrans implements ISaveForTransaction
{
    private  $GoldsAccount = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->GoldsAccount = $record;
        $this->extend_params = $extend_params;
    }
    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->GoldsAccount instanceof GoldsAccount))
        {
            $error = '不是充值记录对象';
            return false;
        }
        $other_pay_bill = $this->extend_params['other_pay_bill'];
        if(empty($other_pay_bill)){
            $error = '第三方充值账单号为空';
            return false;
        }

        $gold_account_id = $this->GoldsAccount->gold_account_id;
        $user_id         = $this->GoldsAccount->user_id;
        $device_type     = $this->extend_params['device_type'];
        $operateType     = 1;
        $operateValue    = $this->extend_params['total_fee'];

        $sql = 'update mb_golds_prestore set other_pay_bill=:ord, status_result=2, remark1=:nn where prestore_id=:rid and status_result = 1';
        $rst =\Yii::$app->db->createCommand($sql,[
            ':ord'=>(string)$other_pay_bill,
            ':rid'=>$this->extend_params['prestore_id'],
            ':nn'=>$this->extend_params['user_name']
        ])->execute();
        if($rst <= 0)
        {
            $sql = \Yii::$app->db->createCommand($sql,[
                ':ord'=>(string)$other_pay_bill,
                ':rid'=>$this->extend_params['prestore_id'],
                ':nn'=>$this->extend_params['user_name']
            ])->rawSql;
            $error = '金币充值订单状态更新失败,单号：'.$other_pay_bill;
            \Yii::getLogger()->log($error.'，sql:'.$sql.'        $other_pay_bill='.$other_pay_bill.'    ---'.is_numeric($other_pay_bill), Logger::LEVEL_ERROR);
            return false;
        }else {
            $integralAccountModel = IntegralAccount::findOne(['user_id'=>$user_id]);
            $goldsPrestoreModel = GoldsPrestore::findOne(['prestore_id'=>$this->extend_params['prestore_id']]);
            if(   GoldsAccountUtil::UpdateGoldsAccountToAdd($gold_account_id, $user_id, $device_type, $operateType, $goldsPrestoreModel->gold_goods_num , $error) )
            {
                if(  $goldsPrestoreModel->extra_integral_num >0 ) {
                    if (!IntegralAccountUtil::UpdateIntegralAccountToAdd(
                        $integralAccountModel->integral_account_id,
                        $user_id,
                        $device_type,
                        $operateType,
                        $goldsPrestoreModel->extra_integral_num,
                        $error)
                    ) {
                        \Yii::getLogger()->log('user_id:' . $user_id . '金币冲值订单other_pay_bill：' . $other_pay_bill . '送积分：' . $goldsPrestoreModel->extra_integral_num . '不成功', Logger::LEVEL_ERROR);
                    }
                }
            }else
            {
                $error = 'user_id:'.$user_id.'金币冲值订单other_pay_bill：'.$other_pay_bill.'金币额度：'.$operateValue.'冲值不成功';
                \Yii::getLogger()->log($error, Logger::LEVEL_ERROR);
                return false;
            }
        }
        return true;
    }
} 