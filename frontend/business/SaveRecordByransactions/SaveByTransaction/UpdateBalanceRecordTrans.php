<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/20
 * Time: 15:43
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;

/**
 * 修改用户余额记录
 * Class ModifyBalanceByTicketToCash
 * @package frontend\business\UserAccountBalanceActions
 */
class UpdateBalanceRecordTrans implements ISaveForTransaction
{
    private  $balance = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->balance = $record;
        $this->extend_params = $extend_params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)//($params,&$balance, &$error)
    {

        $operateType = $this->extend_params['operate_type'];
        $error = '';
        //根据类型判断操作类型是否是第三方修改
        if($operateType)
        {
            if(in_array(intval($operateType),[13,14,15,16,20,30,31]))
            {
                $insertSql = 'insert into mb_update_balance_record ( operate_type, account_balance,create_time,remark1)
VALUES(:op,:ab,:ct,:md)';

                $rst = \Yii::$app->db->createCommand($insertSql,[
                    ':op'=> $operateType,
                    ':ab'=> \Yii::$app->user->identity->username,
                    ':ct' => date('Y-m-d H:i:s'),
                    ':md'=>\Yii::$app->user->id,
                ])->execute();

                if($rst <= 0)
                {
                    $error = '插入修改余额记录失败';
                    return false;
                }
            }
        }

        return true;
    }
}