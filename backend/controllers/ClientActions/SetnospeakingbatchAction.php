<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/12
 * Time: 20:01
 */

namespace backend\controllers\ClientActions;


use backend\business\ClientInfoUtil;
use common\models\Client;
use yii\base\Action;
use common\components\tenxunlivingsdk\TimRestApi;

class SetnospeakingbatchAction extends Action
{
    public function run()
    {
        set_time_limit(0);
        $offset = (int)\Yii::$app->request->get('offset');
        $limit  = (int)\Yii::$app->request->get('limit');

        $query = Client::find();
        $query->select('`client_id`')
              ->andWhere(['status' => 0])
              ->orderBy('`create_time` DESC')
              ->offset($offset)
              ->limit($limit);
        $clients = $query->all();
        $time  = explode(' ',microtime());
        $start = $time[1] + $time[0];
        ob_flush();

        echo '共 ' . count($clients) . ' 条数据' . str_repeat('<', 20);
        echo '<br>';
        foreach ($clients as $client) {
            $id = (string)$client->client_id;
            $rst = TimRestApi::setnospeaking($id);
            echo '<span style="display: inline-block; width: 100px; margin-left: 20px;">' . $id . '</span>';
            echo PHP_EOL;
            echo $rst ? '<span style="color: #080">success</span>' : '<span style="color: red">fail</span>';
            echo str_repeat(' ', 4096);
            echo '<br>';
            flush();
        }
        echo str_repeat('>', 20) . '结束，耗时 ';
        $time  = explode(' ',microtime());
        echo $time[1] + $time[0] - $start;
    }
} 