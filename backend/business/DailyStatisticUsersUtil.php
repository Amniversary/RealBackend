<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/8
 * Time: 9:36
 */

namespace backend\business;


use common\components\SystemParamsUtil;
use common\models\Client;
use common\models\FansStatistics;
use common\models\SystemParams;
use yii\base\Exception;
use yii\db\Query;
use yii\log\Logger;

class DailyStatisticUsersUtil
{
    /**
     * 根据AppId 获取今日昨日粉丝24小时统计
     * @param $appId
     * @return mixed
     */
    public static function getDailyFansNum($appId, $day = 0)
    {
        $db = \Yii::$app->db;
        $sql = 'select sum(net_user) as net_user, sum(new_user) as new_user, sum(cancel_user) as cancel_user, statistics_date
from wc_fans_date where create_time = :date ';
        if ($appId != 0) {
            $sql .= sprintf(' and app_id = %d', $appId);
        }
        $sql .= ' group by statistics_date';
        $sql .= ' order by statistics_date asc';
        $time = $day == 0 ? date('Y-m-d') : date('Y-m-d', strtotime("-$day day"));
        $rst = $db->createCommand($sql, [':date' => $time])->queryAll();
        if (empty($rst)) {
            $rst[] = ['name' => 'empty'];
        }
        $net_user = [];
        $new_user = [];
        $cancel_user = [];
        $date = [];
        for ($i = 0; $i < 24; $i++) {
            foreach ($rst as $item) {
                if (!isset($item['statistics_date']) || $item['statistics_date'] != $i) {
                    $net_user[$i] = 0;
                    $new_user[$i] = 0;
                    $cancel_user[$i] = 0;
                } else {
                    $net_user[$i] = intval($item['net_user']);
                    $new_user[$i] = intval($item['new_user']);
                    $cancel_user[$i] = intval($item['cancel_user']);
                }
            }
            $date[] = $i. ' 时';
        }
        $rstData = [
            'net_user' => ['name' => '净增用户', 'data' => $net_user],
            'new_user' => ['name' => '新增用户', 'data' => $new_user],
            'cancel_user' => ['name' => '减少用户', 'data' => $cancel_user],
            'date' => $date
        ];
        return $rstData;
    }

    /**
     * 根据AppId 获取前7天数据信息
     * @param $appId
     * @return array
     */
    public static function getFansNum($appId, $day = 7)
    {
        $db = \Yii::$app->db;
        $sql = 'select sum(new_user) as new_user, sum(cancel_user) as cancel_user, sum(net_user) as net_user, sum(total_user + net_user) as total_user, statistics_date
        from wc_fans_statistics where statistics_date BETWEEN :start and :end';
        if ($appId != 0) {
            $sql .= sprintf(' and app_id = %d', $appId);
        }
        $sql .= ' group by statistics_date';
        $rst = $db->createCommand($sql, [
            ':start' => date('Y-m-d', strtotime("-$day day")),
            ':end' => date('Y-m-d'),
        ])->queryAll();
        $net_user = [];
        $cancel_user = [];
        $new_user = [];
        $total_user = [];
        $date = [];
        foreach ($rst as $item) {
            $net_user[] = intval($item['net_user']);
            $cancel_user[] = intval($item['cancel_user']);
            $new_user[] = intval($item['new_user']);
            $total_user[] = intval($item['total_user']);
            $date[] = $item['statistics_date'];
        }

        $data = [
            'net_user' => ['name' => '净增用户', 'data' => $net_user],
            'cancel_user' => ['name' => '减少用户', 'data' => $cancel_user],
            'new_user' => ['name' => '新增用户', 'data' => $new_user],
            'total_user' => ['name' => '总用户', 'data' => $total_user],
            'date' => $date
        ];
        return $data;
    }


    /**
     * 合并数据
     * @return mixed
     */
    public static function MergeDailyRecord()
    {
        $recodes = DailyStatisticUsersUtil::GetStatisticRecord();
        $dailys = DailyStatisticUsersUtil::StatisticDailyUsers();
        $daily_time = [];
        $vs = [];
        $date = date('Y-m-d');
        for ($i = 0; $i < 14; $i++) {
            $time[] = date('Y-m-d', strtotime("-$i day"));
        }
        sort($time);
        if (array_key_exists('', $dailys)) {
            $daily_temp[$date] = 0;
            $merges = array_merge($recodes, $daily_temp);
            ksort($merges);
            foreach ($merges as $m => $n) {
                $daily_time[$m] = intval($n);
            }
            //var_dump(array_key_exists('2016-03-31',$dailytime));
            for ($j = 0; $j < 14; $j++) {
                $Exist = array_key_exists($time[$j], $daily_time);
                if ($Exist) {
                    $vs[] = $daily_time[$time[$j]];
                } else {
                    $vs[] = 0;
                }
            }
            return [$time, $vs];
        }
        $merges = array_merge($recodes, $dailys);
        ksort($merges);
        foreach ($merges as $m => $n) {
            $daily_time[$m] = intval($n);
        }
        for ($j = 0; $j < 14; $j++) {
            $Exist = array_key_exists($time[$j], $daily_time);
            if ($Exist) {
                $vs[] = $daily_time[$time[$j]];
            } else {
                $vs[] = 0;
            }
        }
        return [$time, $vs];
    }

    /**
     * 处理日统计用户信息并更新初始日期
     * @param $timenum
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function StatisticDailyWord(&$timenum, &$error)
    {
        $startime = SystemParamsUtil::GetSystemParam('statistic_day_users_date', true);//获取初始时间
        $createtime = date('Y-m-d') . ' 00:00:00'; //获取用户创建时间

        $condition = 'create_time < :et AND create_time >= :startime';
        $query = new Query();
        $sql = $query
            ->select(['date_format(create_time,\'%Y-%m-%d\') as account_time', 'count(*) as usernum'])
            ->from(Client::tableName())
            ->where($condition, [':startime' => $startime, ':et' => $createtime])
            ->groupBy('date_format(create_time,\'%Y-%m-%d\') ASC')
            ->all();
        $timenum = 0;
        if (count($sql) > 0) {
            $trans = \Yii::$app->db->beginTransaction();
            try {

                foreach ($sql as $num) {
                    $timenum++;
                    $model = new UserDailyStatistic();
                    $model->data_time = $num['account_time'];
                    $model->user_day_num = $num['usernum'];

                    if (!DailyStatisticUsersUtil::Save($model, $error)) {
                        throw new Exception($error);
                    }

                }

                $trans->commit();
            } catch (Exception $e) {
                $trans->rollBack();
                $error = $e->getMessage();
                return false;
            }
        }

        $revamp = SystemParams::find()->where(['code' => 'statistic_day_users_date'])->one();
        $revamp->value2 = $createtime;
        if (!$revamp->save()) {
            $error = '更新日统计初始时间失败';
            \Yii::getLogger()->log($error . ':' . var_export($revamp->getErrors()), Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    /**
     * 获取首页公众号统计信息
     * @return array
     */
    public static function getCount()
    {
        $sql = 'select SUM(count_user) as count,SUM(cumulate_user) as cumulate_user from `wc_statistics_count`';
        $rst = \Yii::$app->db->createCommand($sql)->queryOne();
        $count = 'select ifnull(sum(new_user),0) as new_user, ifnull(sum(net_user),0) as net_user from wc_fans_statistics WHERE statistics_date = :date';
        $res = \Yii::$app->db->createCommand($count, [':date' => date('Y-m-d')])->queryOne();
        $arr = array_merge($rst, $res);
        return $arr;
    }
}