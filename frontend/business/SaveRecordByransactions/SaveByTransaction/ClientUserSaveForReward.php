<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/25
 * Time: 17:28
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;

class ClientUserSaveForReward implements ISaveForTransaction
{
    private $getClientRecord = null;
    private $extend_params = [];

    public function __construct($record,$extend_params=[])
    {
        $this->getClientRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {

    }
} 