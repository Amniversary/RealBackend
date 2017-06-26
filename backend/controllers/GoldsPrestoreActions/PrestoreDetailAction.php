<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 16:58
 */

namespace backend\controllers\GoldsPrestoreActions;


use backend\business\GoodsUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;
use frontend\business\ClientUtil;

use frontend\business\RechargeListUtil;

use frontend\business\GoldsPrestoreUtil;
use yii\base\Action;

class PrestoreDetailAction extends Action
{
    public function run($prestore_id)
    {       
        if(!isset($prestore_id))
        {
            ExitUtil::ExitWithMessage('充值id不能为空');
        }  
        //$recharge = RechargeListUtil::GetChargeListById($recharge_id);
        $PrestoreModel = GoldsPrestoreUtil::GetGoldPrestoreModelById($prestore_id); 
        if(!isset($PrestoreModel))
        {
            ExitUtil::ExitWithMessage('金币充值记录不存在');
        }
       
        $client = ClientUtil::GetClientById($PrestoreModel->user_id);
  
        $this->controller->layout='main_empty';
        return $this->controller->render('/goldsprestore/prestoredetail',[
            'client'=> $client,
            'model' =>$PrestoreModel,
        ]);
    }
} 