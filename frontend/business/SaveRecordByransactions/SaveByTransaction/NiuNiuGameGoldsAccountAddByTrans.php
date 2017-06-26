<?php
namespace frontend\business\SaveRecordByransactions\SaveByTransaction;



use frontend\business\GoldsAccountUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;


/**
 * 牛牛游戏用户金币帐户信息添加设置
 * Class NiuNiuGameGoldsAccountAddByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class NiuNiuGameGoldsAccountAddByTrans implements ISaveForTransaction
{
    private  $data=[];

    /**
     * @param $data   //所要修改和插入的数据
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(empty($this->data['operate_type']))
        {
            $this->data['operate_type'] = 3;
        }
        if(!GoldsAccountUtil::UpdateGoldsAccountToAdd($this->data['gold_account_id'],$this->data['user_id'],$this->data['device_type'],$this->data['operate_type'],$this->data['operateValue'],$error))
        {
            $error = var_export($error,true).'GameGoldsAccountAdd  data===:'.var_export($this->data,true);
            return false;
        }
        return true;
    }

}