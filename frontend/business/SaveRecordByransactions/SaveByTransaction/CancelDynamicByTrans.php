<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/10
 * Time: 20:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\DynamicUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;

class CancelDynamicByTrans implements ISaveForTransaction
{
    private $CancelDynamicRecord = null;
    private $extend_params =[];

    public function __construct($CancelDynamicRecord,$extend_params=[])
    {
        $this->CancelDynamicRecord = $CancelDynamicRecord;
        $this->extend_params = $extend_params;
    }


    function SaveRecordForTransaction(&$error,&$outInfo)
    {

        /*$this->CancelDynamicRecord->status = 0;
        if(!DynamicUtil::SaveDynamic($this->CancelDynamicRecord, $error))
        {
            return false;
        }*/
        $upsql = 'update mb_friends_circle set status = 0,remark4 = :rm WHERE user_id = :ud AND dynamic_id in ('.$this->CancelDynamicRecord.')';
        $rst = \Yii::$app->db->createCommand($upsql,[
            ':ud'=>$this->extend_params['user_id'],
            ':rm'=>date('Y-m-d H:i:s')
        ])->execute();

        if($rst <= 0)
        {
            $error = '删除动态照片失败';
            return false;
        }

        $sql = 'delete from mb_new_active where user_id = :ud AND dynamic_id = :md';
        $query = \Yii::$app->db->createCommand($sql,[
            ':ud'=>$this->extend_params['user_id'],
            ':md'=>$this->CancelDynamicRecord,
        ])->execute();


        return true;
    }
} 