<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/21
 * Time: 15:46
 */

namespace frontend\business;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\db\Transaction;
use yii\log\Logger;

class SaveByTransUtil
{
    /**
     * 事物保存支持
     * @param $objList  //需要保存的对象数组，
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function RewardSaveByTransaction($objList,&$error,&$outInfo = null)
    {
        $error ='';
        if(!isset($objList) || !is_array($objList))
        {
            $error = '非法对象，不是数组';
            return false;
        }
        foreach($objList as $obj)
        {
            if(!($obj instanceof ISaveForTransaction))
            {
                $error = '对象数组中存在非ISaveForTransaction对象';
                return false;
            }
        }
        $outInfo = [];
        $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);
        try
        {
            foreach($objList as $obj)
            {
                if(!$obj->SaveRecordForTransaction($error,$outInfo))
                {
                    if(is_array($error))
                    {
                        \Yii::getLogger()->log(var_export($error,true).' type:'.var_export($obj,true),Logger::LEVEL_ERROR);
                    }
                    else
                    {
                        \Yii::getLogger()->log($error.' type:'.var_export($obj,true),Logger::LEVEL_ERROR);
                    }
                    $trans->rollBack();
                    return false;
                }
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            $trans->rollBack();
            return false;
        }
        return true;
    }
} 