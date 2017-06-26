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

class WishSaveByTrans implements ISaveForTransaction
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
        if(!isset($this->extend_params['modify_type']))
        {
            $error = '修改类型参数不能为空';
            return false;
        }
        $modify_type = $this->extend_params['modify_type'];
        try
        {
            if (!WishUtil::WishModify($this->wish, $modify_type, $error, $this->extend_params))
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