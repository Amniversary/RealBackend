<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/15
 * Time: 下午6:50
 */

namespace backend\controllers\PublicListActions;


use yii\base\Action;
use yii\db\Query;

class CompareInfoAction extends Action
{
    public function run()
    {
        ini_set('max_execution_time', '0');
        $rst = ['code'=>1, 'msg'=>''];
        $post = \Yii::$app->request->post('CompareForm');
        if(empty($post)) {
            $rst['msg'] = 'Post 参数不能为空';
            echo json_encode($rst); exit;
        }
        if($post['compare_one'] == $post['compare_two']) {
            $rst['msg'] = '相同的公众不能比对';
            echo json_encode($rst); exit;
        }
        $sum = [];
        $json = [];
        foreach($post as $item) {
            $query = (new Query())->from('wc_client')
                ->select(['client_id', 'nick_name'])
                ->where('app_id = :ap',[':ap'=>$item])
                ->all();
            $data = [];
            $count = count($query);
            foreach($query as $v) {
                $data[$v['client_id']] = $v['nick_name'];
            }
            $json[] = $data;
            $sum[] = $count;
            unset($count);
            unset($data);
        }
        $max = intval($sum[0]) + intval($sum[1]);
        $rst_json = array_unique(array_merge($json[0], $json[1]));
        $count_json = count($rst_json);
        unset($json);
        unset($rst_json);
        $rst['data'] = [
            'count_one' => $sum[0],
            'count_two' => $sum[1],
            'max' => $max,
            'count_json' => $count_json,
            'poor' => $max - $count_json,
        ];
        $rst['code'] = 0;
        \Yii::error(var_export($rst,true));
        echo json_encode($rst);
    }
}