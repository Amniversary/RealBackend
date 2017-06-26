<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7
 * Time: 20:42
 */

namespace frontend\controllers\MblivingActions;


use common\models\StatisticLivingTime;
use yii\base\Action;
use yii\db\Query;

class MbActivityLivingAction extends Action
{
    public function run()
    {
        $params = \Yii::$app->request->get();

        if (!isset($params['unique_no'])) {
            echo  json_encode([
                'status' => 0,
                'error' => '出错啦！'
            ]);
            exit;
        }
        $unique_no = $params['unique_no'];

        $query = StatisticLivingTime::find();
        $query
            ->select('sum(mb_statistic_living_time.living_time) as living_time, mb_client.client_no, mb_client.client_id, pic, nick_name')
            ->innerJoin('mb_client', 'mb_client.client_no = mb_statistic_living_time.client_no')
            ->andWhere(['>=', 'statistic_date', '2017-04-06'])
            ->andWhere(['statistic_type' => 1])
            ->groupBy('client_no')
            ->orderBy('living_time desc')
            ->limit(5);

        $sql = $query->createCommand()->getRawSql();
        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        $query = new Query();
        $query
            ->select('sum(mb_statistic_living_time.living_time) as living_time, mb_client.client_no, mb_client.client_id, pic, nick_name')
            ->from('mb_client')
            ->leftJoin('mb_statistic_living_time', 'mb_client.client_no = mb_statistic_living_time.client_no')
            ->andWhere(['>=', 'statistic_date', '2017-04-06'])
            ->andWhere([
                'statistic_type' => 1,
                'mb_client.unique_no' => $unique_no
            ]);
        $my = $query->one();

        if (empty($my['living_time'])) {
            $my['living_time'] = 0;
        }
        echo  json_encode([
            'status' => 1,
            'data' => [
                'my' => $my,
                'ranking' => $result,
            ]
        ]);
    }
} 