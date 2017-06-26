<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 15:26
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\Wish;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use frontend\business\WishUtil;
use yii\base\Exception;

class WishSaveForToBalance implements ISaveForTransaction
{
    private  $wish = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->wish = $record;
        $this->extend_params = $extend_params;
    }

    public function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $error = '';
        if(!($this->wish instanceof Wish))
        {
            $error = '不是愿望对象，数据异常';
            return false;
        }
        try
        {
            if (!WishUtil::WishModify($this->wish, 'to_balance', $error, $this->extend_params))
            {
                throw new Exception($error);
            }
        }
        catch(Exception $e)
        {
            throw $e;
        }
        if(!isset($outInfo))
        {
            $outInfo = [];
        }
        $outInfo['wish'] = $this->wish;
        return true;
    }
} 