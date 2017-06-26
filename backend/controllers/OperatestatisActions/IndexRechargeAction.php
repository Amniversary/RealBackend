<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/18
 * Time: 17:54
 */
namespace backend\controllers\OperatestatisActions;

use frontend\business\OperateStatisUtil;
use yii\base\Action;

class IndexRechargeAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '运用数据统计';

        //充值数据的统计
        $RechargeNumDate = '';
        $RechargeNumDate['three_RechargeNumDate'] = OperateStatisUtil::GetThreeRechargeNumDate();
        $RechargeNumDate['seven_RechargeNumDate'] = OperateStatisUtil::GetSevenRechargeNumDate();
        $RechargeNumDate['thirty_RechargeNumDate'] = OperateStatisUtil::GetThirtyRechargeNumDate();
        $RechargeNumDate['one_house_RechargeNumDate'] = OperateStatisUtil::GetOneHouseRechargeNumDate();
        $RechargeNumDate['yesterday_house_RechargeNumDate'] = OperateStatisUtil::GetYesterdayHouseRechargeNumDate();

        return $this->controller->render('index_recharge',[
            'RechargeNumDate' => $RechargeNumDate,
        ]);
    }
}