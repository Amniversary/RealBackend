<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/8
 * Time: 18:54
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;

use frontend\business\ExperienceLogUtil;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\db\Query;

class AddregnumDaySaveByTrans implements ISaveForTransaction
{
//    private $CommentRecord = null;
//    private $extend_params = [];

//    public function __construct($Comment,$extend_params=[])
//    {
//        $this->CommentRecord = $Comment;
//        $this->extend_params = $extend_params;
//    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        //根据当天获取本周的的日期
        $tswk = date('Y-W');
        //根据本周一获取本周的日期
        $tswk_1 = date('Y-W',strtotime(date('Y-m-d',(time()-((date('w')==0?7:date('w'))-1)*24*3600))));

        if($tswk != $tswk_1)
        {
            $tswk = $tswk_1;
        }

        //查找数据库的统计时间 查看是否有与当前时间一致的统计时间
        $query = (new Query())
            ->select(['statistics_time', 'statistics_type'])
            ->from('mb_add_reg_num')
            ->where('statistics_time in (:day,:week,:month,:house)', [':day' => date('Y-m-d'), ':week' => $tswk, ':month' => date('Y-m'),':house'=>date('Y-m-d H')])
            ->all();

        foreach($query as $v)
        {
            if($v['statistics_type'] == 3)
            {
                $query['month'] = $v;
            }
            if($v['statistics_type'] == 2)
            {
                $query['week'] = $v;
            }
            if($v['statistics_type'] == 1)
            {
                $query['day'] = $v;
            }
            if($v['statistics_type'] == 4)
            {
                $query['house'] = $v;
            }
        }


        //判断本周
        if(!empty($query['week']) && $query['week']['statistics_time'] == $tswk)
        {
            $updateDay = 'update mb_add_reg_num set statistics_num = statistics_num+1 where statistics_time= :timed and statistics_type = 2';
            $update_result = \Yii::$app->db->createCommand($updateDay,[
                ':timed' => $tswk,
            ])->execute();

            if($update_result <= 0){
                $error = '修改注册人数失败';
                return false;
            }

        }
        else
        {
            $insertDay = 'INSERT INTO mb_add_reg_num(statistics_type,statistics_time,statistics_num) VALUES (2,:timed,1)';;
            $insert_result = \Yii::$app->db->createCommand($insertDay,[
                'timed' => $tswk,
            ])->execute();

            if($insert_result <= 0){
                $error = '新增注册人数记录失败';
                return false;
            }
        }

        //判断本月
        if(!empty($query['month']))
        {
            $updateDay = 'update mb_add_reg_num set statistics_num = statistics_num+1 where statistics_time= :timed and statistics_type = 3';
            $update_result = \Yii::$app->db->createCommand($updateDay,[
                ':timed' => date('Y-m'),
            ])->execute();

            if($update_result <= 0){
                $error = '修改注册人数失败';
                return false;
            }

        }
        else
        {
            $insertDay = 'INSERT INTO mb_add_reg_num(statistics_type,statistics_time,statistics_num) VALUES (3,:timed,1)';;
            $insert_result = \Yii::$app->db->createCommand($insertDay,[
                'timed' => date('Y-m'),
            ])->execute();

            if($insert_result <= 0){
                $error = '新增注册人数记录失败';
                return false;
            }
        }

        //判断本日
        if(!empty($query['day']))
        {
            $updateDay = 'update mb_add_reg_num set statistics_num = statistics_num+1 where statistics_time= :timed and statistics_type = 1';
            $update_result = \Yii::$app->db->createCommand($updateDay,[
                ':timed' => date('Y-m-d'),
            ])->execute();

            if($update_result <= 0){
                $error = '修改注册人数失败';
                return false;
            }
        }
        else
        {
            $insertDay = 'INSERT INTO mb_add_reg_num(statistics_type,statistics_time,statistics_num) VALUES (1,:timed,1)';;
            $insert_result = \Yii::$app->db->createCommand($insertDay,[
                'timed' => date('Y-m-d'),
            ])->execute();

            if($insert_result <= 0){
                $error = '新增注册人数记录失败';
                return false;
            }
        }

        //判断是否有这个小时
        if (!empty($query['house'])) {
            $updateDay = 'update mb_add_reg_num set statistics_num = statistics_num+1 where statistics_time= :timed and statistics_type = 4';
            $update_result = \Yii::$app->db->createCommand($updateDay, [
                ':timed' => date('Y-m-d H'),
            ])->execute();

            if ($update_result <= 0) {
                echo '修改注册人数失败';
                return false;
            }
        } else {
            $insertDay = 'INSERT INTO mb_add_reg_num(statistics_type,statistics_time,statistics_num) VALUES (4,:timed,1)';;
            $insert_result = \Yii::$app->db->createCommand($insertDay, [
                'timed' => date('Y-m-d H'),
            ])->execute();

            if ($insert_result <= 0) {
                echo '新增注册人数记录失败';
                return false;
            }
        }

        return true;
    }

}