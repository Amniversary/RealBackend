<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/10
 * Time: 20:08
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\RedCircleReward;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class RedDynamicRewardSaveByTrans implements ISaveForTransaction
{
    private $RedDynamicRecord = null;
    private $extend_params = [];

    public function __construct($RedDynamicRecord,$extend_params=[])
    {
        $this->RedDynamicRecord = $RedDynamicRecord;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $model = new RedCircleReward();
        $model->attributes = $this->RedDynamicRecord;
        $model->create_time = date('Y-m-d H:i:s');


        if(!$model->save())
        {
            $error = '保存红包动态打赏记录失败';
            \Yii::getLogger()->log($error. ' :'.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        $update = 'update mb_friends_circle set check_num = check_num + 1 WHERE dynamic_id = :md';
        $query = \Yii::$app->db->createCommand($update,[
            ':md' => $model->dynamic_id
        ])->execute();

        if($query <= 0)
        {
            $error = '更新红包动态打赏次数失败';
            \Yii::getLogger()->log($error.' :'.\Yii::$app->db->createCommand($update,[
                    ':md'=>$model->dynamic_id
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
} 