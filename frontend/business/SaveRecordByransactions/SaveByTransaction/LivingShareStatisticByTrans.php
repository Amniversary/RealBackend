<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 16:51
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;

class LivingShareStatisticByTrans implements ISaveForTransaction
{
    private $getShareRecord = null;
    private $extend_params = [];

    public function __construct($record,$extend_params=[])
    {
        $this->getShareRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        $joinSql = 'insert ignore into mb_statistic_sharesource (wechat_no,qzone_no,weibo_no,qq_no,wx_circle_no,date_time) VALUES (0,0,0,0,0,:tm)';

        $query = \Yii::$app->db->createCommand($joinSql,[
            ':tm'=>$this->getShareRecord['time'],
        ])->execute();

        $upSql = 'update mb_statistic_sharesource set ';
        switch(intval($this->getShareRecord['share_type']))
        {
            case 1:
                $upSql .= 'wechat_no = wechat_no + 1 ';
                break;
            case 2:
                $upSql .= 'qq_no = qq_no + 1 ';
                break;
            case 3:
                $upSql .= 'qzone_no = qzone_no + 1 ';
                break;
            case 4:
                $upSql .= 'wx_circle_no = wx_circle_no + 1 ';
                break;
            case 5:
                $upSql .= 'weibo_no = weibo_no + 1 ';
                break;
        }
        $upSql .= 'where date_time = :tt';

        $upQuery = \Yii::$app->db->createCommand($upSql,[
            ':tt'=>$this->getShareRecord['time'],
        ])->execute();

        if($upQuery <= 0)
        {
            $error = '更新分享统计信息失败';
            \Yii::getLogger()->log($error.' : share_type:'.$this->getShareRecord['share_type'].\Yii::$app->db->createCommand($upSql,[
                    ':tt'=>$this->getShareRecord['time'],
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }
} 