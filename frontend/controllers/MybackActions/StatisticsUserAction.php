<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10
 * Time: 15:48
 */
  namespace frontend\controllers\MybackActions;

  use backend\business\DailyStatisticUsersUtil;
  use backend\business\MonthStatisticUsersUtil;
  use backend\business\VerifyMonthUserUtil;
  use backend\business\VisitorsStatisticUsersUtil;
  use common\components\PhpLock;
  use yii\base\Action;
  use yii\log\Logger;

  class StatisticsUserAction extends Action
{

    /**
     * 检查参数
     * @param $error
     * @return bool
     */
    private function check_post_params(&$error)
    {
        $error = '';
        //$rand_str, $time, $token,$data,$token_other
        if(!isset($_GET['backvelidatekey']) || empty($_GET['backvelidatekey']))
        {
            $error = '参数缺少';
            \Yii::getLogger()->log('lost param:'.var_export($_GET,true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    public function run()
    {
        set_time_limit(0);
        $error = '';
        if(!$this->check_post_params($error))
        {
            $rst['errno'] = '1';
            $rst['errmsg'] =$error;
            \Yii::getLogger()->log($rst['errmsg'],Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        $key = $_GET['backvelidatekey'];
        if($key !== \Yii::$app->params['backvelidatekey'])
        {
            \Yii::getLogger()->log('验证key不正确',Logger::LEVEL_ERROR);
            return;
        }

        $lock = new PhpLock('statisticusers');
        $lock->lock();
        DailyStatisticUsersUtil::StatisticDailyWord($timenum,$error);
        MonthStatisticUsersUtil::StatisticsMonthWord($error);
        VisitorsStatisticUsersUtil::VerifyNumber($error);
        VerifyMonthUserUtil::VisitorsMonthNumber($error);
        $lock->unlock();
        echo 'statisticusers,number:'. $timenum .' '. date('Y-m-d');
    }
}