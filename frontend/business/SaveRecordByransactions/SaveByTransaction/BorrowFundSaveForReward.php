<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/4
 * Time: 21:02
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\BorrowFund;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;

class BorrowFundSaveForReward implements ISaveForTransaction
{
    private  $borrowFund = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->borrowFund = $record;
        $this->extend_params = $extend_params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->borrowFund instanceof BorrowFund))
        {
            $error = '借款记录对象';
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
                if(array_key_exists($attr,$this->borrowFund->attributes) &&
                    isset($outInfo[$objeKey]) &&
                    is_object($outInfo[$objeKey])&&
                    isset($outInfo[$objeKey]->$objeKeyAttr))
                {
                    if(isset($otherObj['key_method']))
                    {
                        $keyMethod = $otherObj['key_method'];
                        if(method_exists($this->borrowFund,$keyMethod))
                        {
                            $this->borrowFund->$keyMethod($outInfo[$objeKey]->$objeKeyAttr);
                        }
                    }
                    else
                    {
                        $this->borrowFund[$attr] = $outInfo[$objeKey]->$objeKeyAttr;
                    }

                }
            }
        }
        if(!$this->borrowFund->save())
        {
            \Yii::getLogger()->log(var_export($this->borrowFund->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception('借款记录保存失败');
        }
        if(!isset($outInfo))
        {
            $outInfo = [];
        }
        $outInfo['borrow_fund'] = $this->borrowFund;
        return true;
    }
} 