<?php

namespace backend\controllers\IntegralAccountActions;


use frontend\business\GoldsAccountUtil;
use frontend\business\IntegralAccountUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;

use yii\base\Action;
/**
 * 修改人员
 * Class EditAction
 * @package backend\controllers\EditAction
 */
class EditAction extends Action
{
    public function run($integral_account_id)
    {
        $model = IntegralAccountUtil::GetIntegralAccountModleByIntergralId($integral_account_id);
        if(!isset($model)){
            ExitUtil::ExitWithMessage('积分帐户不存在');
        }

        $IntegralAccount  = \Yii::$app->request->post("IntegralAccount");
        if( $model->account_status == 1 && $IntegralAccount ){
                    $integral_account_balance = intval( $IntegralAccount['integral_account_balance'] );
                    if($integral_account_balance > $model->integral_account_balance ){
                         $user_id       = $model->user_id;
                         $device_type   = 3;
                         $operateType   = 1;
                         $operateValue  = $integral_account_balance - $model->integral_account_balance;
                         $returnVal =  IntegralAccountUtil::UpdateIntegralAccountToAdd($integral_account_id, $user_id, $device_type, $operateType, $operateValue, $error);
                         if( $returnVal ){
                              return $this->controller->redirect(['index']);
                         }else{
                              echo "充值时发生了错误,请联系系统管理员";
                         }
                    }
        }else{
             return $this->controller->render('update', [
                'model' => $model,
            ]);
        } 
    }
} 