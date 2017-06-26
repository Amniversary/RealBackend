<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/12
 * Time: 20:01
 */

namespace backend\controllers\ClientActions;


use backend\business\ClientInfoUtil;
use frontend\business\ClientUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UpdateClientInfoSaveByTrans;
use yii\base\Action;
use common\components\tenxunlivingsdk\TimRestApi;

class SetnospeakingAction extends Action
{
    public function run()
    {
        $client_id = (string)\Yii::$app->request->post('client_id');

        $rst = ['code'=>'1','message'=>''];
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
        $C2CTime = \Yii::$app->request->post('C2Cmsg_nospeaking_time') * 1;
        $groupTime = \Yii::$app->request->post('groupmsg_nospeaking_time') * 1;
        $imResult = TimRestApi::setnospeaking($client_id, $C2CTime, $groupTime);
        if (!$imResult) {
            $rst['message'] = '设置禁言失败';
            echo json_encode($rst);
            exit;
        }

        $imResult = TimRestApi::getnospeaking($client_id);
        if (!$imResult) {
            $rst['message'] = '查询禁言失败';
            echo json_encode($rst);
            exit;
        }

        $rst['code'] = '0';
        $rst['data'] = $imResult;
        echo json_encode($rst);
    }
} 