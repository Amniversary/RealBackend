<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/9
 * Time: 18:48
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\FriendsCircle;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class DynamicSaveByTrans implements ISaveForTransaction
{
    private $DynamicRecord = null;
    private $extend_params = [];

    public function __construct($dynamic, $extend_params = [])
    {
        $this->DynamicRecord = $dynamic;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!$this->DynamicRecord instanceof FriendsCircle)
        {
            $error = '不是动态信息对象';
            return false;
        }

        if(!$this->DynamicRecord->save())
        {
            $error = '动态信息保存失败';
            \Yii::getLogger()->log($error. ' :'.var_export($this->DynamicRecord->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        $joinSql = 'insert ignore into mb_new_active (user_id) VALUES (:ud)';

        $query = \Yii::$app->db->createCommand($joinSql,[
            ':ud'=>$this->DynamicRecord->user_id,
        ])->execute();

        $upSql = 'update mb_new_active set dynamic_id = :dld WHERE user_id = :udd';
        $upQuery = \Yii::$app->db->createCommand($upSql,[
            ':dld'=>$this->DynamicRecord->dynamic_id,
            ':udd'=>$this->DynamicRecord->user_id,
        ])->execute();

        if($upQuery <= 0)
        {
            $error = '更新最新动态失败';
            \Yii::getLogger()->log($error. ' : dynamic_id:'.$this->DynamicRecord->dynamic_id.'--'.\Yii::$app->db->createCommand($upQuery,[
                    ':dld'=>$this->DynamicRecord->dynamic_id,
                    ':udd'=>$this->DynamicRecord->user_id,
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
} 