<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\ClientActions;

use backend\business\GoodsUtil;
use frontend\business\ClientUtil;
use yii\base\Action;

class SetInnerAction extends Action
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
            $rst['message'] = '没有Goods模型对应的数据';
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
        if(!isset($dataItem['is_inner']))
        {
            $rst['message'] = '用户类型值为空';
            echo json_encode($rst);
            exit;
        }
        $is_inner = $dataItem['is_inner'];
        $client->is_inner = $is_inner;
        if(!ClientUtil::SaveClient($client,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        $info = \Yii::$app->cache->get('mb_api_login_'.$client->unique_no);
        if($info !== false)
        {
            $info = unserialize($info);
            $ary = [
                'device_no'=>$info['device_no'],
                'user_id'=>$client->client_id,
                'verify_code'=>$info['verify_code'],
                'client_type'=>$client->client_type,
                'unique_no' => $client->unique_no,
                'nick_name' =>$client->nick_name,
                'is_inner'=>$client->is_inner
            ];
            $str = serialize($ary);
            \Yii::$app->cache->set('mb_api_login_'.$client->unique_no,$str);
            $loginInfo = \Yii::$app->cache->get('mb_api_login_'.$client->unique_no);
            if(!isset($loginInfo) || empty($loginInfo))
            {
                $rst['message'] = '缓存登录信息异常,缓存失败';
                echo json_encode($rst);
                exit;
            }
        }

        echo '0';
    }
} 