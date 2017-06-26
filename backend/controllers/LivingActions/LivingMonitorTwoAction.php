<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/20
 * Time: 10:12
 */

namespace backend\controllers\LivingActions;


use frontend\business\LivingUtil;
use yii\base\Action;

class LivingMonitorTwoAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '直播监控室3';

        $data = LivingUtil::GetLiveLiving();

        return $this->controller->render('livingmonitortwo',
            [
                'data' => $data,
            ]
        );
    }
}