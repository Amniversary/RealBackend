<?php
/**
 * Created by PhpStorm.
 * User: zff
 * Date: 2016/8/12
 * Time: 13:00
 */

namespace console\controllers\BackendActions;


use common\components\UsualFunForNetWorkHelper;
use frontend\business\ChatPersonGroupUtil;
use yii\base\Action;
use yii\db\Query;
use yii\log\Logger;

class ProduceRobotsAction extends Action
{

    public function run()
    {
/*        $robots = $this->getRobots();
        var_dump(count($robots));
        exit;*/
        set_time_limit(0);
        $stat = microtime(true);
        $current = 1; //生成的批次
        $sleep_time = 1000000; //多长时间执行一次，以微秒为单位，500毫秒

        while($current<=1000) {
            $robots = $this->getRobots();
            $robots = json_encode($robots);

            //存储到缓存
            $result = \Yii::$app->cache->set('living_robot_'.$current, $robots);
            if(!$result){
                \Yii::getLogger()->log('living_robot_'.$current.'批次机器人存储异常', Logger::LEVEL_ERROR);
            }
            //echo " robot is: $current\n";
            //生成下一批次
            $current++;
            //休息1000毫秒
            usleep($sleep_time);


        }
        $end = microtime(true);
        $time = $end - $stat;
        $date = date('Y-m-d H:i:s');
        echo " Create Robot is ok! ------ time:$time ----- date:$date\n";
    }

    //随机获得55个机器人
    public function getRobots()
    {
        $query = (new Query())
            ->select(['client_id as user_id','unique_no','register_type','nick_name','level_no as level_id'])
            ->from('mb_client bc')
            ->innerJoin('mb_client_active ca','bc.client_id = ca.user_id')
            ->join('join','(SELECT ROUND(RAND() * (SELECT MAX(client_id) FROM `mb_client`)) + 100 AS id) AS t2')
            ->where(['and','status = 1','level_no < 15','bc.client_id between t2.id - 100 and t2.id'])
            ->orderBy('bc.client_id ASC')
            ->limit(55)
            ->all();

        return $query;
    }
} 