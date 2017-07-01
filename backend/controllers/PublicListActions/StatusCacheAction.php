<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/30
 * Time: 下午6:42
 */

namespace backend\controllers\PublicListActions;


use yii\base\Action;

class StatusCacheAction extends Action
{
    public function run($record_id)
    {
        $rst = ['code'=>'1','msg'=>''];
        if(empty($record_id)){
            $rst['msg'] = '记录Id不能为空';
            echo json_encode($rst);
            exit;
        }

        $user_id = \Yii::$app->user->id;

        $data = [
            'record_id'=>$record_id,
            'backend_id'=>$user_id,
        ];

        \Yii::$app->cache->set('app_backend_'.$user_id,json_encode($data));
        $rst['code'] = 0;
        echo json_encode($rst);
    }
}