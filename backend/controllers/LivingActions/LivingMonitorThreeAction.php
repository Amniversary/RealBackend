<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/20
 * Time: 10:13
 */

namespace backend\controllers\LivingActions;


use frontend\business\LivingUtil;
use yii\base\Action;

class LivingMonitorThreeAction extends Action
{
    public function run()
    {
        $this->controller->getView()->title = '直播监控室4';

        $data = LivingUtil::GetLiveLiving();

        return $this->controller->render('livingmonitorthree',
            [
                'data' => $data,
            ]
        );
    }
}