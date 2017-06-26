<?php

namespace backend\controllers\GoldsAccountActions;


use frontend\business\GoldsAccountUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;
use common\models\GoldsAccount;

use yii\base\Action;
/**
 * 修改人员
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class BackAction extends Action
{
    public function run($gold_account_id)
    {
        $model = GoldsAccount::findOne(['gold_account_id'=>$gold_account_id]);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('金币帐户不存在');
        }

        $GoldsAccount  = \Yii::$app->request->post("GoldsAccount");
        $gold_account_balance_less = intval( $GoldsAccount['gold_account_balance_less'] );
        if( $model->account_status == 1 && $gold_account_balance_less>0  && $model->gold_account_balance >= $gold_account_balance_less ){
            $user_id       = $model->user_id;
            $device_type   = 3;
            $operateType   = 6;
            $operateValue  = $gold_account_balance_less;
            $returnVal =  GoldsAccountUtil::UpdateGoldsAccountToLessen($gold_account_id, $user_id, $device_type, $operateType, $operateValue, $error);
            if( $returnVal ){
                return $this->controller->redirect(['index']);
            }else{
                return $this->controller->redirect(['index']);
            }
        }else{
            return $this->controller->render('back', [
                'model' => $model,
            ]);
        }
    }
}