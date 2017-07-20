<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/19
 * Time: 上午11:47
 */

namespace backend\controllers\PublicListActions;


use common\models\StatisticsCount;
use yii\base\Action;

class SetCountAction extends Action
{
    public function run($app_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($app_id)) {
            $rst['message'] = '记录id 不能为空';
            echo json_encode($rst);
            exit;
        }
        $data = StatisticsCount::findOne(['app_id'=>$app_id]);
        if(!isset($data)) {
            $rst['message'] = '统计记录不存在';
            echo json_encode($rst);
            exit;
        }
        $count_user = \Yii::$app->request->post('count_user');
        if(!isset($count_user)){
            $rst['message'] = '获取消息值错误';
            echo json_encode($rst);
            exit;
        }

        $data->count_user = $count_user;
        $data->cumulate_user = $count_user;
        $data->update_time = date('Y-m-d H:i:s');
        if(!($data instanceof StatisticsCount))
        {
            $rst['message'] = '不是统计记录对象';
            echo json_encode($rst);
            exit;
        }
        if(!$data->save()){
            $rst['message'] = '保存总粉丝数失败';
            \Yii::error($rst['message'] . ': '.var_export($data->getErrors(),true));
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
}