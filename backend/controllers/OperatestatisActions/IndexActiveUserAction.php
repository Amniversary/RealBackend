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

class IndexActiveUserAction extends Action
{
    public function run()
    {

        $this->controller->getView()->title = '活跃用户数据的统计';

        //活跃主播数据的统计
        $ActiveUserNumDate = '';
        $ActiveUserNumDate['three_ActiveUserNumDate'] = OperateStatisUtil::GetThreeActiveUserNumDate();
        $ActiveUserNumDate['seven_ActiveUserNumDate'] = OperateStatisUtil::GetSevenActiveUserNumDate();
        $ActiveUserNumDate['thirty_ActiveUserNumDate'] = OperateStatisUtil::GetThirtyActiveUserNumDate();
        $ActiveUserNumDate['one_house_ActiveUserNumDate'] = OperateStatisUtil::GetOneHouseActiveUserNumDate();
        $ActiveUserNumDate['yesterday_house_ActiveUserNumDate'] = OperateStatisUtil::GetYesterdayHouseActiveUserNumDate();


        return $this->controller->render('index_activeuser',[
            'ActiveUserNumDate' => $ActiveUserNumDate,
        ]);
    }
}