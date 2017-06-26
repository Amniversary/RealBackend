<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/2
 * Time: 18:53
 */
namespace backend\controllers\ClientActions;

use common\models\Client;
use yii\base\Action;

class SetSignNameAction extends Action
{
    public function run($client_id)
    {
        if(!isset($client_id) || empty($client_id))
        {
            $message = '用户id不能为空';
            echo json_encode(array('message' => $message));
            exit;
        }
        $index = \Yii::$app->request->post('editableIndex');
        $client = \Yii::$app->request->post('Client');
        if(!isset($index))
        {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $client_model = Client::findOne(['client_id' => $client_id]);
        if(empty($client_model))
        {
            $message = '用户记录不存在';
            echo json_encode(array('message' => $message));
            exit;
        }

        $client_model->sign_name = strval($client[$index]['sign_name']);
        if(!$client_model->save())
        {
            $message = var_export($client_model->getErrors(),true);
            echo json_encode(array('message' => $message));
            exit;
        }
        echo '0';
    }
}