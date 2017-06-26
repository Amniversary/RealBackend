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

class IndexActiveAnchorAction extends Action
{
    public function run()
    {

        $this->controller->getView()->title = '活跃主播数据的统计';

        //活跃主播数据的统计
        $ActiveAnchorNumDate = '';
        $ActiveAnchorNumDate['three_ActiveAnchorNumDate'] = OperateStatisUtil::GetThreeActiveAnchorNumDate();
        $ActiveAnchorNumDate['seven_ActiveAnchorNumDate'] = OperateStatisUtil::GetSevenActiveAnchorNumDate();
        $ActiveAnchorNumDate['thirty_ActiveAnchorNumDate'] = OperateStatisUtil::GetThirtyActiveAnchorNumDate();
        $ActiveAnchorNumDate['one_house_ActiveAnchorNumDate'] = OperateStatisUtil::GetOneHouseActiveAnchorNumDate();
        $ActiveAnchorNumDate['yesterday_house_ActiveAnchorNumDate'] = OperateStatisUtil::GetYesterdayHouseActiveAnchorNumDate();


        return $this->controller->render('index_activeanchor',[
            'ActiveAnchorNumDate' => $ActiveAnchorNumDate,
        ]);
    }
}