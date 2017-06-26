<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\ClientActions;

use frontend\business\ClientUtil;
use yii\base\Action;
use yii\log\Logger;

class SetClientTypeAction extends Action
{
    public function run($client_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($client_id))
        {
            $rst['message'] = '用户id不能为空';
            echo json_encode($rst);
            exit;
        }
        $client = ClientUtil::GetClientById($client_id);
        if(!isset($client))
        {
            $rst['message'] = '用户不存在';
            echo json_encode($rst);
            exit;
        }

        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit))
        {
            $rst['message'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        if(empty($hasEdit))
        {
            $rst['message'] = '';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex))
        {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }
        $modifyData = \Yii::$app->request->post('Client');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有Client模型对应的数据';
            echo json_encode($rst);
            exit;
        }

        if(!isset($modifyData[$editIndex]))
        {
            $rst['message'] = '对应的列下没有数据';
            echo json_encode($rst);
            exit;
        }
        $dataItem = $modifyData[$editIndex];
        if(!isset($dataItem['client_type']))
        {
            $rst['message'] = '用户类型值为空';
            echo json_encode($rst);
            exit;
        }
        $client_type = $dataItem['client_type'];
        $client->client_type = $client_type;
        if(!ClientUtil::SaveClient($client,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }

        $uniqueNo = $client->unique_no;
        $nick_name = $client->nick_name;
        $is_inner = $client->is_inner;
        $loginInfo = \Yii::$app->cache->get('mb_api_login_'.$uniqueNo);
        if($loginInfo !== false)
        {
            $login = unserialize($loginInfo);
            $ary = [
                'device_no'=>$login['device_no'],
                'verify_code' => $login['verify_code'],
                'user_id'=>$client_id,
                'client_type'=>$client_type,
                'unique_no' =>$uniqueNo,
                'nick_name' =>$nick_name,
                'is_inner'=>$is_inner,
            ];
            $str = serialize($ary);
            \Yii::$app->cache->set('mb_api_login_'.$uniqueNo, $str,30*24*3600);//保持一个月
            $loginInfoStr = \Yii::$app->cache->get('mb_api_login_'.$uniqueNo);
            if(!isset($loginInfoStr) || empty($loginInfoStr))
            {
                $rst['message'] = '存储登录信息异常，修改超管失败';
                echo json_encode($rst);
                exit;
            }
        }
        echo '0';
    }
} 