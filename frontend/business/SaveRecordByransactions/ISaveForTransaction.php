<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 15:15
 */

namespace frontend\business\SaveRecordByransactions;

/**
 * Interface ISaveForTransaction 事物提交接口
 * @package frontend\business\RewardTransactions
 */
interface ISaveForTransaction
{
    function SaveRecordForTransaction(&$error,&$outInfo);
} 