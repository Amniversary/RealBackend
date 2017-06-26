<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/4
 * Time: 21:02
 */

namespace frontend\business\SaveRecordByransactions\SaveForRewardByTransaction;


use common\models\BorrowFund;
use common\models\BusinessCheck;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;

class CheckRecordSaveForReward implements ISaveForTransaction
{
    private  $businessCheck = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->businessCheck = $record;
        $this->extend_params = $extend_params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->businessCheck instanceof BusinessCheck))
        {
            $error = '审核记录对象';
            return false;
        }
        if(isset($this->extend_params) &&
            isset($this->extend_params['propertys']) &&
            is_array($this->extend_params['propertys'])
        )
        {
            foreach($this->extend_params['propertys'] as $attr => $otherObj)
            {
                $objeKey = $otherObj['model'];
                $objeKeyAttr = $otherObj['attr'];
                if(array_key_exists($attr,$this->businessCheck->attributes) &&
                    isset($outInfo[$objeKey]) &&
                    is_object($outInfo[$objeKey])&&
                    isset($outInfo[$objeKey]->$objeKeyAttr))
                {
                    if(isset($otherObj['key_method']))
                    {
                        $keyMethod = $otherObj['key_method'];
                        if(method_exists($this->businessCheck,$keyMethod))
                        {
                            $this->businessCheck->$keyMethod($outInfo[$objeKey]->$objeKeyAttr);
                        }
                    }
                    else
                    {
                        $this->businessCheck[$attr] = $outInfo[$objeKey]->$objeKeyAttr;
                    }
                }
            }
        }
        if(!$this->businessCheck->save())
        {
            \Yii::getLogger()->log(var_export($this->businessCheck->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception('借款记录保存失败');
        }
        return true;
    }
} 