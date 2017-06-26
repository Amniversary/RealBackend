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

class GetnospeakingAction extends Action
{
    public function run($client_id)
    {
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

        $imResult = TimRestApi::getnospeaking((string)$client_id);
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