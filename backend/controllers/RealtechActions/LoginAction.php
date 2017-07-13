<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/11
 * Time: 上午11:36
 */

namespace backend\controllers\RealtechActions;


use backend\models\RealtechLoginSearch;
use yii\base\Action;

class LoginAction extends Action
{
    public function run()
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:*');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $rst = ['code'=>'1','msg'=>''];
        $rawpostdata = file_get_contents("php://input");
        $post = json_decode($rawpostdata,true);
        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            \Yii::$app->response->statusCode = 200;
            echo json_encode($rst);
            exit;
        }
        $model = new RealtechLoginSearch();
        if(!$model->load($post)){
            \Yii::error('post::'.var_export($post,true));
            $rst['msg'] = '无法加载对象数据';
            echo json_encode($rst);
            exit;
        }
        if(!$model->validatePassword($error)){
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = '0';
        echo json_encode($rst);
    }
}