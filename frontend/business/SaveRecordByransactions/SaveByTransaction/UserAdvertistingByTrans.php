<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\LivingHot;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\log\Logger;

/**
 * 弹窗广告用记记录表写数据
 * Class UserAdvertistingByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class UserAdvertistingByTrans implements ISaveForTransaction
{
    private  $extend_params=[];

    /**
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($extend_params=[])
    {
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $inser_sql = 'insert ignore into mb_user_ad_images(ad_id,user_id) VALUES (:aid,:uid)';
        $update_sql = 'update mb_user_ad_images set create_time=:ctime WHERE ad_id=:aid and user_id=:uid';

        \Yii::$app->db->createCommand($inser_sql,
            [
                ':aid' => $this->extend_params['ad_id'],
                ':uid' => $this->extend_params['user_id'],
            ])->execute();
        $rst = \Yii::$app->db->createCommand($update_sql,
            [
                ':aid' => $this->extend_params['ad_id'],
                ':uid' => $this->extend_params['user_id'],
                ':ctime' => date('Y-m-d H:i:s')
            ])->execute();
        if($rst <= 0)
        {
            $error = '弹窗广告用户记录写入失败';
            \Yii::getLogger()->log($error.'  sql==:'.\Yii::$app->db->createCommand($update_sql,
                    [
                        ':aid' => $this->extend_params['ad_id'],
                        ':uid' => $this->extend_params['user_id'],
                        ':ctime' => date('Y-m-d H:i:s')
                    ])->rawSql,Logger::LEVEL_ERROR);
        }
        return true;
    }
} 