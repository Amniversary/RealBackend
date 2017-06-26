<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\ClientActions;

use frontend\business\ClientUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\UnbindWeCatOrAlipaySaveByTrans;
use yii\base\Action;

class UnbindWeCatAction extends Action
{
    public function run()
    {
        $client_id = \Yii::$app->request->post('client_id');
        $unbind_type = \Yii::$app->request->post('unbind_type');
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
        $data = [
            'client_id' => $client_id,
            'unbind_type' =>$unbind_type
        ];
        $transActions[] = new UnbindWeCatOrAlipaySaveByTrans($data);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo,$error))
        {

            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }

        $rst['code'] = 0;
        echo json_encode($rst);
    }
}