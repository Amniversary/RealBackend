<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:46
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\BusinessLog;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

class BusinessLogSaveForReward implements ISaveForTransaction
{
    private  $businessLog = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->businessLog = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->businessLog instanceof BusinessLog))
        {
            $error = '非业务日志记录对象';
            return false;
        }
        if(!isset($this->extend_params) || !isset($this->extend_params['error']))
        {
            $error = '参数错误，异常内容不能为空';
            return false;
        }
        $errMsg = $this->extend_params['error'];
        if(isset($this->extend_params) &&
            isset($this->extend_params['propertys']) &&
            is_array($this->extend_params['propertys'])
        )
        {
            foreach($this->extend_params['propertys'] as $attr => $otherObj)
            {
                $objeKey = $otherObj['model'];
                $objeKeyAttr = $otherObj['attr'];
                if(array_key_exists($attr,$this->businessLog->attributes) &&
                    isset($outInfo[$objeKey]) &&
                    is_object($outInfo[$objeKey])&&
                    isset($outInfo[$objeKey]->$objeKeyAttr))
                {
                    if(isset($otherObj['key_method']))
                    {
                        $keyMethod = $otherObj['key_method'];
                        if(method_exists($this->businessLog,$keyMethod))
                        {
                            $this->businessLog->$keyMethod($outInfo[$objeKey]->$objeKeyAttr);
                        }
                    }
                    else
                    {
                        $value_php_fun = null;
                        if(isset($otherObj['value_php_fun']) &&
                            !empty($otherObj['value_php_fun']) &&
                            function_exists($otherObj['value_php_fun']))
                        {
                            $value_php_fun = $otherObj['value_php_fun'];//执行系统函数功能
                            $this->businessLog[$attr] = $value_php_fun($outInfo[$objeKey]->$objeKeyAttr);
                        }
                        else
                        {
                            $this->businessLog[$attr] = $outInfo[$objeKey]->$objeKeyAttr;
                        }
                    }
                }
            }
        }
        if(!$this->businessLog->save())
        {
            \Yii::getLogger()->log(var_export($this->businessLog->getErrors(),true), Logger::LEVEL_ERROR);
            throw new Exception($errMsg);
        }

        return true;
    }
} 