<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/18
 * Time: 17:50
 */
namespace backend\controllers\OperatestatisActions;

use frontend\business\OperateStatisUtil;
use yii\base\Action;

class IndexAddregnumAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '注册数据统计';

        //注册数据的统计
        $AddregnumDate = '';
        $AddregnumDate['three_AddregnumDate'] = OperateStatisUtil::GetThreeAddregnumDate();
        $AddregnumDate['seven_AddregnumDate'] = OperateStatisUtil::GetSevenAddregnumDate();
        $AddregnumDate['thirty_AddregnumDate'] = OperateStatisUtil::GetThirtyAddregnumDate();
        $AddregnumDate['one_house_AddregnumDate'] = OperateStatisUtil::GetOneHouseAddregnumDate();
        $AddregnumDate['yesterday_house_AddregnumDate'] = OperateStatisUtil::GetYesterdayHouseAddregnumDate();


        return $this->controller->render('index_addregnum',[
            'AddregnumDate' => $AddregnumDate,
        ]);
    }
}