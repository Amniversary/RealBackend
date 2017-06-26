<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 16:58
 */

namespace backend\controllers\CheckMoneyGoodsActions;


use backend\business\GoodsUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;
use frontend\business\ClientUtil;
use frontend\business\RechargeListUtil;
use yii\base\Action;

class RechargeDetailAction extends Action
{
    public function run($recharge_id)
    {
        if(!isset($recharge_id))
        {
            ExitUtil::ExitWithMessage('充值id不能为空');
        }
        $recharge = RechargeListUtil::GetChargeListById($recharge_id);
        if(!isset($recharge))
        {
            ExitUtil::ExitWithMessage('充值记录不存在');
        }
        $client = ClientUtil::GetClientById($recharge->user_id);

        $this->controller->layout='main_empty';
        return $this->controller->render('rechargedetail',[
            'client'=> $client,
            'model' =>$recharge,
        ]);
    }
} 