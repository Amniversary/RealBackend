<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 15:15
 */

namespace frontend\business\SaveRecordByTransactions;

/**
 * Interface ISaveForTransaction 事物提交接口
 * @package frontend\business\SaveRecordByTransactions
 */
interface ISaveForTransaction
{
    function SaveRecordForTransaction(&$error,&$outInfo);
} 