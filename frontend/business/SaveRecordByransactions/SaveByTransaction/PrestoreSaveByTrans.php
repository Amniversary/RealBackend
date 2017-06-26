<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/23
 * Time: 15:00
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use common\models\GoldsGoods;
use common\models\GoldsPrestore;

use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class PrestoreSaveByTrans implements ISaveForTransaction
{
    private  $golds_goods = null;
    private  $other_pay_bill= null;
    private  $user_id = null;
    private  $status = null;
    private  $pay_bill = null;
    private  $receipt_data = null;

    /**
     * @param $record
     * @param array $other_pay_bill
     * @throws Exception
     */
    public function __construct($record,$other_pay_bill,$user_id,$status=2,$pay_bill='',$receipt_data='')
    {
        $this->golds_goods = $record;
        $this->other_pay_bill = $other_pay_bill;
        $this->user_id = $user_id;
        $this->status = $status;
        $this->pay_bill = $pay_bill;
        $this->receipt_data = $receipt_data;
        if(empty($pay_bill))
        {
            $this->pay_bill = WaterNumUtil::GenWaterNum('ZHF-RG-',true,true,date('Y-m-d'),4);
        }


    }
    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->golds_goods instanceof GoldsGoods))
        {
            $error = '不是商品对象';
            return false;
        }

        $unique_op_no = UsualFunForStringHelper::CreateGUID();

        if(empty($this->other_pay_bill))
        {
            $error = '第三方充值账单号为空';
            return false;
        }

        $sql = 'insert ignore into mb_golds_prestore (user_id,gold_goods_id,gold_goods_name,gold_goods_price,gold_goods_num,extra_integral_num,pay_money,status_result,pay_type,pay_bill,other_pay_bill,create_time,op_unique_no,pay_times,fail_reason,remark2)
         values(:uid,:gid,:gname,:gprice,:gnum,:bnum,:pmoney,:status,:ptype,:pbill,:opbill,:ctime,:ounique,:ptime,:reason,:mark2)';

        $rst =\Yii::$app->db->createCommand($sql,[
            ':uid'=>$this->user_id,
            ':gid'=>$this->golds_goods->gold_goods_id,
            ':gname'=>$this->golds_goods->gold_goods_name,
            ':gprice'=>$this->golds_goods->gold_goods_price,
            ':gnum'=>1,
            ':bnum'=>$this->golds_goods->extra_integral_num,
            ':pmoney'=>$this->golds_goods->gold_goods_price,
            ':status'=>$this->status,
            ':ptype'=>6,
            ':pbill'=>$this->pay_bill,
            ':opbill'=>$this->other_pay_bill,
            ':ctime'=>date('Y-m-d H:i:s'),
            ':ounique'=>$unique_op_no,
            ':ptime'=>1,
            ':reason'=>'',
            ':mark2' => $this->receipt_data
        ])->execute();

        if($rst <= 0)
        {
            $error = '充值失败';
            return false;
        }

        $sql = 'SELECT LAST_INSERT_ID()';
        $re = \Yii::$app->db->createCommand($sql)->queryScalar();

        $outInfo['prestore_id'] = $re;
        return true;
    }
}


