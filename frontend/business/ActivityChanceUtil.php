<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 16:31
 */

namespace frontend\business;

use common\models\ActivityChance;
use common\models\ActivityInfo;
use common\models\ActivityPrize;
use yii\db\Query;
use yii\log\Logger;

class ActivityChanceUtil {

    /**
     * 通过ID获得信息
     * @param $chance_id
     * @return null|static
     */
    public static function GetActivityChanceById($chance_id)
    {
        return ActivityChance::findOne(['chance_id' => $chance_id]);
    }

    /**
     * 保存抽奖活动机会信息
     * @param $user_id
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public static function ActivityChanceSave($user_id,&$outInfo,&$error)
    {
        $activity_info = ActivityUtil::GetActivityByType(3);   //类型为3的是大转盘抽奖
        if(!isset($activity_info) || empty($activity_info))
        {
            $error = '活动类型不存在';
            \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
            return false;
        }
        $activity_chance = new ActivityChance();
        $activity_chance->user_id = $user_id;
        $activity_chance->activity_id = $activity_info['activity_id'];
        $activity_chance->number = 1;
        $activity_chance->create_time = date('Y-m-d H:i:s');
        if(!$activity_chance->save())
        {
            $error = '抽奖活动机会表写入失败';
            \Yii::getLogger()->log($error.var_export($activity_chance,true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 删除奖品信息缓存
     * @param $error
     * @return bool
     */
    public static function DeleteActivityPrizeCache(&$error)
    {
        $cache = \Yii::$app->cache->delete('get_prize_info');
        if(!$cache)
        {
            $error = '奖品信息缓存删除失败';
            return false;
        }
        return true;
    }

    /**
     * 执行抽奖动作
     * @param $activity_id
     * @param $error
     * @return array
     */
    public static function DoLottery($activity_id,&$error)
    {
        $activity_prize = ActivityUtil::GetActivityPrizeInfo($activity_id);     //得到所有奖品信息
        if(!isset($activity_prize) || empty($activity_prize))
        {
            $error = '奖品信息不存在';
            \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
            return [];
        }

        $float_len = 0;
        $prize_arr = [];
        foreach($activity_prize as $key=>$prize)
        {
            $prize_arr[$key] = $prize['rate'];
            $temp = explode ( '.', $prize['rate'] );
            $float_len = (($float_len < strlen($temp[1]))?strlen($temp[1]):$float_len);
        }
        $key = LuckyGiftUtil::GetRandRate($prize_arr,$float_len);
        $prize_info = $activity_prize[$key];    //中奖的奖品信息
        return $prize_info;
    }

    /**
     * 得到抽奖次数
     * @param $activity_id
     * @return array
     */
    public static function GetActivityRecordNumber($activity_id)
    {
        $query = (new Query())
            ->from('mb_lucky_draw_record')
            ->select(['count(record_id) as number'])
            ->where('activity_id=:aid',[':aid'=>$activity_id])
            ->all();
        return $query;
    }

    /**
     * 统计大转盘抽奖信息写入
     * @param $activity_id
     * @param $field
     * @param $number
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function SetActivityRecordStatisticSave($activity_id,$field,&$error)
    {
        $sql = 'insert ignore into mb_activity_statistic (activity_id,field_name,number,create_time) values(:aid,:field,:num,:ctime)';
        $res = \Yii::$app->db->createCommand($sql,[
            ':aid' => $activity_id,
            ':field' => $field,
            ':num' => 0,
            ':ctime' =>date('Y-m-d H:i:s')
        ])->execute();
        $sql = 'update mb_activity_statistic set number = number+1 where activity_id=:aid and field_name=:field';
        $res = \Yii::$app->db->createCommand($sql,[
            ':aid' => $activity_id,
            ':field' => $field,
        ])->execute();
        if($res <= 0)
        {
            $error = '抽奖统计写入失败';
            \Yii::getLogger()->log($error.\Yii::$app->db->createCommand($sql,[
                    ':aid' => $activity_id,
                    ':field' => $field,
                ])->rawSql,Logger::LEVEL_ERROR);
        }
        return true;
    }


} 