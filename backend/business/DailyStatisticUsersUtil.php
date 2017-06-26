<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/8
 * Time: 9:36
 */

namespace backend\business;


use backend\components\ExitUtil;
use common\components\QrCodeUtil;
use common\components\SystemParamsUtil;
use common\models\AccountInfo;
use common\models\Client;
use common\models\SystemParams;
use common\models\UserDailyStatistic;
use yii\base\Exception;
use yii\db\Query;
use yii\log\Logger;

class DailyStatisticUsersUtil {

    /**
     * 当日统计人数
     * @return array
     */
    public static function StatisticDailyUsers()
    {
        $dailyUsers = [];
        $condition = 'DATE_FORMAT(create_time,\'%Y-%m-%d\') = DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
        $query =new Query();
        $sql = $query
            ->select(['COUNT(*) as usernum','DATE_FORMAT(create_time,\'%Y-%m-%d\') as dailytime'])
            ->from(Client::tableName())
            ->where($condition)
            ->all();
        foreach($sql as $s)
        {
            $dailyUsers[$s['dailytime']] = $s['usernum'];
        }
        return $dailyUsers;
    }


    /**
     * 查询当天前13条记录
     * @return array
     */
    public static function GetStatisticRecord()
    {
        $condition = 'DATE_FORMAT(data_time,\'%Y-%m-%d\') < DATE_FORMAT(NOW(),\'%Y-%m-%d\')';
        $query =new Query();
        $sql = $query
            ->select(['user_day_num','data_time'])
            ->from(UserDailyStatistic::tableName())
            ->where($condition)
            ->orderBy('data_time desc')
            ->limit(13)
            ->all();
        $record = [];
        foreach($sql as $s)
        {
            $record[$s['data_time']] =  $s['user_day_num'];
        }
        return $record;
    }

    /**
     * 获取注册总人数
     * @return number
     */
    public static function StatisticsSumUser()
    {
        list($time,$sum) = MonthStatisticUsersUtil::MergeMonthRecord();
        return array_sum($sum);
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
        for($i = 0; $i < 14 ; $i ++)
        {
            $time[] = date('Y-m-d',strtotime("-$i day"));
        }
        sort($time);
        if(array_key_exists('',$dailys)){
            $daily_temp[$date] = 0;
            $merges = array_merge($recodes,$daily_temp);
            ksort($merges);
            foreach($merges as $m => $n)
            {
                $daily_time[$m] = intval($n) ;
            }
            //var_dump(array_key_exists('2016-03-31',$dailytime));
            for($j = 0; $j < 14; $j++){
                $Exist = array_key_exists($time[$j],$daily_time);
                if($Exist)
                {
                    $vs[]= $daily_time[$time[$j]];
                }
                else
                {
                    $vs[] = 0;
                }
            }
            return [$time,$vs];
        }
        $merges = array_merge($recodes,$dailys);
        ksort($merges);
        foreach($merges as $m => $n )
        {
            $daily_time[$m] = intval($n);
        }
        for($j = 0; $j < 14; $j++){
            $Exist = array_key_exists($time[$j],$daily_time);
            if($Exist)
            {
                $vs[]= $daily_time[$time[$j]];
            }
            else
            {
                $vs[] = 0;
            }
        }
        return [$time,$vs];
    }

    /**
     * 处理日统计用户信息并更新初始日期
     * @param $timenum
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function StatisticDailyWord(&$timenum,&$error)
    {
        $startime = SystemParamsUtil::GetSystemParam('statistic_day_users_date',true);//获取初始时间
        $createtime = date('Y-m-d').' 00:00:00'; //获取用户创建时间

        $condition = 'create_time < :et AND create_time >= :startime';
        $query =new Query();
        $sql = $query
            ->select(['date_format(create_time,\'%Y-%m-%d\') as account_time','count(*) as usernum'])
            ->from(Client::tableName())
            ->where($condition,[':startime'=>$startime,':et'=>$createtime])
            ->groupBy('date_format(create_time,\'%Y-%m-%d\') ASC')
            ->all();
        $timenum = 0;
        if(count($sql) > 0)
        {
            $trans = \Yii::$app->db->beginTransaction();
            try {

                foreach ($sql as $num)
                {
                    $timenum++;
                    $model = new UserDailyStatistic();
                    $model->data_time = $num['account_time'];
                    $model->user_day_num = $num['usernum'];

                    if (!DailyStatisticUsersUtil::Save($model, $error))
                    {
                        throw new Exception($error);
                    }

                }

                $trans->commit();
            }
            catch (Exception $e)
            {
                $trans->rollBack();
                $error = $e->getMessage();
                return false;
            }
        }

        $revamp = SystemParams::find()->where(['code'=>'statistic_day_users_date'])->one();
        $revamp->value2 = $createtime;
        if(!$revamp->save()){
            $error = '更新日统计初始时间失败';
            \Yii::getLogger()->log($error . ':' . var_export($revamp->getErrors()),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    /**
     * 保存统计信息
     * @param $model
     * @param $error
     * @return bool
     */
    public static function Save($model,&$error)
    {

        if (!$model instanceof UserDailyStatistic)
        {
            $error = '不是统计信息记录';
            return false;
        }
        if (!$model->save())
        {
            $error = '保存统计信息失败';
            \Yii::getLogger()->log($error . ' :' . var_export($model->getErrors(), true), Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }
}