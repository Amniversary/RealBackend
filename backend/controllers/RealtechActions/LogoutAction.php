<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/18
 * Time: 下午4:24
 */

namespace backend\controllers\RealtechActions;


use yii\base\Action;

class LogoutAction extends Action
{
    public function run($token)
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:*');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $rst = ['code'=>'1','msg'=>''];
        \Yii::$app->cache->delete($token);
        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
}