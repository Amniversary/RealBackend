<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/31
 * Time: 下午12:09
 */

namespace backend\controllers\ArticleActions;


use backend\business\AuthorizerUtil;
use backend\business\DailyStatisticUsersUtil;
use common\models\AuthorizationList;
use yii\base\Action;

class IndexFansAction extends Action
{
    public function run()
    {
        $app_id = \Yii::$app->request->get('app_id');
        if(empty($app_id)) $app_id = 0;
        $this->controller->getView()->title = '粉丝数据统计';
        $authList = AuthorizerUtil::getAuthListName();
        $day = [0,1,7,14,30];
        foreach($day as $list) {
            switch($list) {
                case 0:$ToDay = DailyStatisticUsersUtil::getDailyFansNum($app_id, 0);break;
                case 1:$Yesterday = DailyStatisticUsersUtil::getDailyFansNum($app_id, 1);break;
                case 7:$WeekNum = DailyStatisticUsersUtil::getFansNum($app_id,7);break;
                case 14:$FourTeen = DailyStatisticUsersUtil::getFansNum($app_id,14);break;
                case 30:$Thirty = DailyStatisticUsersUtil::getFansNum($app_id,30);break;
            }
        }
        return $this->controller->render('index_fans',[
            'app_id' => $app_id,
            'authList' => $authList,
            'ToDay' => $ToDay,
            'Yesterday' => $Yesterday,
            'WeekNum' => $WeekNum,
            'FourTeen' => $FourTeen,
            'Thirty' => $Thirty,
        ]);
    }
}