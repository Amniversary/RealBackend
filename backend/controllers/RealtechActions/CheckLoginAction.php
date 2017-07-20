<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/18
 * Time: 下午6:07
 */

namespace backend\controllers\RealtechActions;


use yii\base\Action;

class CheckLoginAction extends Action
{
    public function run($token)
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:*');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $key = '123123123';
        $rst = ['code'=>'1','msg'=>''];
        $cache = \Yii::$app->cache->get($token);
        if($cache === false) {
            echo json_encode($rst);
            exit;
        }

        $md5 = md5($cache.$key);
        if($md5 != $token) {
            $rst['msg'] = 'token校验失败';
            echo json_encode($rst);
            exit;
        }

        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
}