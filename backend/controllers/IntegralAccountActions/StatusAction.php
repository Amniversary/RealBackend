<?php

namespace backend\controllers\IntegralAccountActions;


use frontend\business\IntegralAccountUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;

use yii\base\Action;
/**
 * 修改金币商品
 * Class StatusAction
 * @package backend\controllers\StatusAction
 */
class StatusAction extends Action
{
    public function run($integral_account_id){
        
        $rst =['message'=>'','output'=>''];
        if(empty($integral_account_id)){
            $rst['message'] = '积分帐户id不能为空';
            echo json_encode($rst);
            exit;
        }
        
        $integralAccount = IntegralAccountUtil::GetIntegralAccountModleByIntergralId($integral_account_id);
        if(!isset($integralAccount))
        {
            $rst['message'] = '积分帐户不存在';
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
        $modifyData = \Yii::$app->request->post('IntegralAccount');
        if(!isset($modifyData))
        {
            $rst['message'] = '没有IntegralAccount模型对应的数据';
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
        if(!isset($dataItem['account_status']))
        {
            $rst['message'] = '状态值为空';
            echo json_encode($rst);
            exit;
        }
        $status = $dataItem['account_status'];
        $integralAccount->account_status = $status;
        if(!$integralAccount->save()){
            $rst['message'] = '更新错误';
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 