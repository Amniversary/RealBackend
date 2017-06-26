<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/15
 * Time: 0:22
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class NiuNiuGameUpdateSaveByTrans implements ISaveForTransaction
{
    private  $extend_params = [];

    public function __construct($extend_params)
    {
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $sql = 'update mb_niuniu_game set is_normal = :mt WHERE record_id = :rid';
        $rst =\Yii::$app->db->createCommand($sql,[
            ':mt'=>$this->extend_params['is_normal'],
            ':rid' => $this->extend_params['record_id']

        ])->execute();

        if($rst <= 0)
        {
            $error = '游戏异常数据信息处理失败';
            \Yii::getLogger()->log($error.'   sql===:'.\Yii::$app->db->createCommand($sql,[
                    ':mt'=>$this->extend_params['is_normal'],
                    ':rid' => $this->extend_params['record_id']

                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
} 